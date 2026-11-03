<?php

namespace App\Http\Controllers\Doctor\Appointments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Appointments\SaveDoctorAppointmentRequest;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use App\Models\DoctorPatient;
use App\Models\DoctorPayment;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleException;
use App\Models\DoctorService;
use App\Models\Patient;
use App\Models\PatientDirectoryEvent;
use App\Models\User;
use App\Notifications\PatientAccountCreated;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DoctorAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $doctor = $request->user('doctor');
        $date = $request->validate(['date' => ['nullable', 'date_format:Y-m-d'], 'view' => ['nullable', Rule::in(['day', 'list', 'week', 'month'])], 'status' => ['nullable', Rule::in(array_keys(DoctorAppointment::STATUSES))], 'search' => ['nullable', 'string', 'max:100'], 'payment_status' => ['nullable', Rule::in(['paid', 'partial', 'unpaid'])], 'arrival' => ['nullable', Rule::in(['arrived', 'not_arrived'])], 'weekday' => ['nullable', 'integer', 'between:0,6'], 'categories' => ['nullable', 'array'], 'categories.*' => [Rule::in(['available', 'limited', 'partial', 'off'])]]);
        $day = CarbonImmutable::parse($date['date'] ?? CarbonImmutable::today(config('thera.schedule_timezone'))->toDateString(), config('thera.schedule_timezone'));
        $mode = $date['view'] ?? 'month';
        $mode = $mode === 'day' ? 'list' : $mode;
        $start = match ($mode) {
            'week' => $day->startOfWeek(CarbonImmutable::SUNDAY), 'month' => $day->startOfMonth(), default => $day
        };
        $end = match ($mode) {
            'week' => $start->addWeek(), 'month' => $start->addMonth(), default => $start->addDay()
        };
        $query = DoctorAppointment::query()->whereBelongsTo($doctor)->where('starts_at', '>=', $start->utc())->where('starts_at', '<', $end->utc());
        $summary = ['total' => (clone $query)->count(), 'confirmed' => (clone $query)->where('status', 'confirmed')->count(), 'arrived' => (clone $query)->whereNotNull('arrived_at')->count(), 'cancelled' => (clone $query)->where('status', 'cancelled')->count()];
        $present = (clone $query)->where('status', 'confirmed')->whereNotNull('arrived_at')->with(['doctor', 'queueEntry', 'visit'])->withSum('payments as paid_minor', 'amount_minor')->get();
        $summary['ready'] = $present->filter(fn ($item) => $item->queueState() === 'ready')->count();
        $summary['waiting'] = $present->filter(fn ($item) => $item->queueState() === 'waiting_payment')->count();
        $appointments = $query->with(['doctor', 'doctorFacility.facility', 'queueEntry', 'visit'])->withSum('payments as paid_minor', 'amount_minor')->when($request->filled('status'), fn ($query) => $query->where('status', $date['status']))
            ->when($request->filled('payment_status'), function (Builder $query) use ($date): void {
                $net = DoctorPayment::query()->selectRaw('COALESCE(SUM(amount_minor), 0)')->whereColumn('doctor_appointment_id', 'doctor_appointments.id');
                if ($date['payment_status'] === 'paid') {
                    $query->where('price_minor', '<=', $net);
                } elseif ($date['payment_status'] === 'partial') {
                    $query->where('price_minor', '>', $net)->where($net, '>', 0);
                } else {
                    $query->where('price_minor', '>', 0)->where($net, '<=', 0);
                }
            })
            ->when($request->filled('arrival'), fn ($query) => $date['arrival'] === 'arrived' ? $query->whereNotNull('arrived_at') : $query->whereNull('arrived_at'))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('patient_name', 'like', '%'.$date['search'].'%')->orWhere('patient_phone', 'like', '%'.$date['search'].'%')))
            ->orderBy('starts_at')->orderBy('id')->paginate($mode === 'list' ? 20 : 200)->appends($request->except('collection'));
        $calendarDays = [];
        if ($mode !== 'list') {
            for ($cursor = $start; $cursor->lessThan($end); $cursor = $cursor->addDay()) {
                if ($mode === 'week' && $request->filled('weekday') && $cursor->dayOfWeek !== (int) $date['weekday']) {
                    continue;
                }
                $calendarDays[] = ['date' => $cursor, 'appointments' => $appointments->getCollection()->filter(fn ($appointment) => $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->isSameDay($cursor))];
            }
        }

        $monthOverview = $mode === 'month' ? $this->monthOverview($doctor, $day) : null;
        $selectedCollection = $this->selectedCollection($request);

        return view('doctor.appointments.index', compact('appointments', 'doctor', 'day', 'mode', 'summary', 'calendarDays', 'monthOverview', 'selectedCollection'));
    }

    /** @return array<string, mixed> */
    private function monthOverview(Doctor $doctor, CarbonImmutable $day): array
    {
        $start = $day->startOfMonth();
        $end = $start->addMonth();
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $records = DoctorAppointment::query()->whereBelongsTo($doctor)->where('starts_at', '>=', $start->utc())->where('starts_at', '<', $end->utc())
            ->get(['doctor_facility_id', 'starts_at', 'status'])->groupBy(fn ($appointment) => $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->toDateString());
        $memberships = $doctor->facilityMemberships()->where('membership_status', DoctorFacility::STATUS_ACTIVE)
            ->whereHas('facility', fn ($query) => $query->where('is_active', true)->where('bookings_enabled', true))
            ->with(['schedule.exceptions', 'services' => fn ($query) => $query->where('is_active', true)])->get();
        $days = [];
        $totalCapacity = 0;
        $occupied = 0;
        $gridStart = $start->startOfWeek(CarbonImmutable::SUNDAY);
        $gridEnd = $end->subDay()->endOfWeek(CarbonImmutable::SATURDAY)->startOfDay();
        for ($cursor = $gridStart; $cursor->lessThanOrEqualTo($gridEnd); $cursor = $cursor->addDay()) {
            $inMonth = $cursor->month === $start->month;
            $daily = $records->get($cursor->toDateString(), collect());
            $capacity = 0;
            $remaining = 0;
            $special = false;
            $closingTime = '';
            $exceptionTypes = [];
            $hasActiveSchedule = false;
            $hasWorkingPeriods = false;
            foreach ($memberships as $membership) {
                $schedule = $membership->schedule;
                if (! $schedule || ! $schedule->is_active || $doctor->verification_status !== 'verified') {
                    continue;
                }
                $hasActiveSchedule = true;
                $exception = $schedule->exceptions->first(fn ($item) => $item->date->isSameDay($cursor));
                if ($exception) {
                    $exceptionTypes[] = $exception->type;
                }
                $periods = $schedule->periodsForDate($cursor);
                $hasWorkingPeriods = $hasWorkingPeriods || count($periods) > 0;
                $hasBookablePeriod = collect($periods)->contains(function (array $period) use ($membership): bool {
                    $longest = collect(DoctorSchedule::windows($period))->max(fn ($window) => $window[1] - $window[0]) ?? 0;

                    return $membership->services->contains(fn ($service) => in_array($service->id, $period['service_ids'], true) && $service->duration_minutes <= $longest);
                });
                if (! $hasBookablePeriod) {
                    continue;
                }
                $capacity += $schedule->daily_capacity;
                $used = $daily->where('doctor_facility_id', $membership->id)->where('status', '!=', 'cancelled')->count();
                $remaining += max(0, $schedule->daily_capacity - $used);
                if ($exception?->type === DoctorScheduleException::TYPE_SPECIAL) {
                    $special = true;
                    $closingTime = max($closingTime, collect($periods)->max('end') ?? '');
                }
            }
            $category = $capacity === 0 ? 'off' : ($remaining === 0 || $remaining / $capacity <= 0.2 ? 'limited' : ($special ? 'partial' : 'available'));
            $full = $capacity > 0 && $remaining === 0;
            $offLabel = in_array('leave', $exceptionTypes, true) ? 'إجازة' : (in_array('closure', $exceptionTypes, true) ? 'إغلاق طارئ' : 'إجازة أسبوعية');
            if (! $hasActiveSchedule || ($capacity === 0 && $hasWorkingPeriods)) {
                $offLabel = 'غير متاح';
            }
            $days[] = ['date' => $cursor, 'inMonth' => $inMonth, 'count' => $daily->count(), 'capacity' => $capacity, 'remaining' => $remaining, 'category' => $category, 'full' => $full, 'special' => $special, 'closingTime' => $closingTime, 'offLabel' => $offLabel, 'today' => $cursor->isSameDay($today)];
            if ($inMonth) {
                $totalCapacity += $capacity;
                $occupied += $capacity - $remaining;
            }
        }

        return ['days' => $days, 'count' => $records->sum(fn ($items) => $items->count()), 'todayCount' => DoctorAppointment::query()->whereBelongsTo($doctor)->where('starts_at', '>=', $today->utc())->where('starts_at', '<', $today->addDay()->utc())->count(), 'occupancy' => $totalCapacity > 0 ? (int) round(100 * $occupied / $totalCapacity) : null];
    }

    public function create(Request $request): View
    {
        return $this->form($request);
    }

    public function contacts(Request $request): JsonResponse
    {
        $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $search = $request->string('q')->trim()->limit(100)->toString();
        $searchId = preg_match('/^(?:PT-)?([1-9][0-9]*)$/i', $search, $matches) ? $matches[1] : null;
        $contacts = DoctorPatient::query()->whereBelongsTo($request->user('doctor'))
            ->where(fn ($query) => $query->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%')->orWhere('id', $searchId))
            ->select(['id', 'name', 'phone'])->orderBy('name')->orderBy('id')->limit(25)->get()
            ->map(fn ($contact) => ['id' => $contact->id, 'text' => $contact->reference().' · '.$contact->name.' — '.$contact->phone, 'name' => $contact->name, 'phone' => $contact->phone]);

        return response()->json(['results' => $contacts])->header('Cache-Control', 'private, no-store');
    }

    public function edit(Request $request, DoctorAppointment $doctorAppointment): View
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorAppointment);

        return $this->form($request, $doctorAppointment);
    }

    public function theraPatients(Request $request): JsonResponse
    {
        abort_unless($request->user('doctor')->verification_status === 'verified', 403);
        $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $search = strtoupper(trim($request->string('q')->toString()));
        $isPhone = preg_match('/^\+?[0-9]{8,15}$/', $search) === 1;
        $publicId = str_starts_with($search, 'TH-') ? substr($search, 3) : $search;
        if (! $isPhone && ! Str::isUlid($publicId)) {
            return response()->json(['results' => []])->header('Cache-Control', 'private, no-store');
        }
        $patients = Patient::query()->where('is_active', true)->where('booking_enabled', true)->whereNotNull('phone_verified_at')
            ->when($isPhone, fn ($query) => $query->where('phone', $search), fn ($query) => $query->where('public_id', $publicId))
            ->select(['id', 'public_id', 'name', 'phone'])->orderBy('id')->limit(25)->get();
        foreach ($patients as $patient) {
            PatientDirectoryEvent::create(['doctor_id' => $request->user('doctor')->id, 'patient_id' => $patient->id, 'action' => 'booking_lookup']);
        }

        return response()->json(['results' => $patients->map(fn ($patient) => ['id' => $patient->public_id, 'text' => $patient->name.' — '.$patient->maskedPhone().' · '.$patient->reference()])])->header('Cache-Control', 'private, no-store');
    }

    private function form(Request $request, ?DoctorAppointment $appointment = null): View
    {
        $doctor = $request->user('doctor');
        $services = DoctorService::query()->whereHas('doctorFacility', fn ($query) => $query->whereBelongsTo($doctor))->bookingEnabled()->with('doctorFacility.facility')->orderBy('name')->get();
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $selectedPatient = null;
        if (! $appointment && $request->filled('patient')) {
            $request->validate(['patient' => ['integer', 'min:1']]);
            $selectedPatient = DoctorPatient::whereBelongsTo($doctor)->findOrFail($request->integer('patient'));
        }

        return view('doctor.appointments.create', ['doctor' => $doctor, 'services' => $services, 'appointment' => $appointment, 'selectedPatient' => $selectedPatient, 'today' => $today->toDateString(), 'maxDate' => $today->addYear()->toDateString(), 'requestKey' => (string) Str::uuid()]);
    }

    public function show(Request $request): View
    {
        $request->validate(['appointment' => ['nullable', 'integer', 'min:1']]);
        $appointment = null;
        if ($request->integer('appointment')) {
            $appointment = DoctorAppointment::query()->with(['doctor', 'doctorFacility.facility', 'events', 'queueEntry', 'visit'])->withSum('payments as paid_minor', 'amount_minor')->findOrFail($request->integer('appointment'));
            Gate::forUser($request->user('doctor'))->authorize('view', $appointment);
        }

        $selectedCollection = $this->selectedCollection($request);
        abort_if($selectedCollection && $selectedCollection->id !== $appointment?->id, 404);

        return view('doctor.appointments.show', ['doctor' => $request->user('doctor'), 'appointment' => $appointment, 'selectedCollection' => $selectedCollection]);
    }

    private function selectedCollection(Request $request): ?DoctorAppointment
    {
        $request->validate(['collection' => ['nullable', 'integer', 'min:1']]);
        if (! $request->filled('collection')) {
            return null;
        }

        return DoctorAppointment::query()->whereBelongsTo($request->user('doctor'))
            ->with(['doctorFacility.facility', 'queueEntry', 'payments.reversal'])
            ->withSum('payments as paid_minor', 'amount_minor')->findOrFail($request->integer('collection'));
    }

    public function availability(Request $request): JsonResponse
    {
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $data = $request->validate(['doctor_service_id' => ['required', 'integer'], 'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today->toDateString(), 'before_or_equal:'.$today->addYear()->toDateString()], 'appointment' => ['nullable', 'integer']]);
        $service = $this->ownService($request->user('doctor'), (int) $data['doctor_service_id']);
        $appointment = null;
        if ($request->integer('appointment')) {
            $appointment = DoctorAppointment::query()->findOrFail($request->integer('appointment'));
            Gate::forUser($request->user('doctor'))->authorize('update', $appointment);
            if ($appointment->doctor_service_id === $service->id) {
                $service->duration_minutes = $appointment->duration_minutes;
            }
        }
        $schedule = $service->doctorFacility->schedule;
        $preview = $schedule ? $schedule->preview(CarbonImmutable::parse($data['date'], config('thera.schedule_timezone')), $service, $appointment?->id)
            : ['status' => 'غير متاح', 'slots' => [], 'capacity' => 0, 'note' => 'احفظ جدول المنشأة أولًا من صفحة الدوام والجدول.'];

        return response()->json($preview)->header('Cache-Control', 'private, no-store');
    }

    public function store(SaveDoctorAppointmentRequest $request): JsonResponse|RedirectResponse
    {
        $appointment = DB::transaction(function () use ($request): DoctorAppointment {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $fingerprint = hash('sha256', json_encode($request->safe()->except('request_key'), JSON_THROW_ON_ERROR));
            $existing = DoctorAppointment::query()->whereBelongsTo($doctor)->where('request_key', $request->validated('request_key'))->first();
            if ($existing) {
                if (! hash_equals($existing->request_fingerprint, $fingerprint)) {
                    throw ValidationException::withMessages(['request_key' => 'استُخدم مفتاح الحجز لبيانات مختلفة. افتح نموذج حجز جديد.']);
                }

                return $existing;
            }
            $service = $this->ownService($doctor, $request->integer('doctor_service_id'));
            $start = $this->availableStart($service, $request->validated('date'), $request->validated('time'));
            $newPatient = in_array($request->validated('patient_mode'), ['new', 'new_thera'], true);
            $theraPatient = null;
            if ($request->validated('patient_mode') === 'thera') {
                $theraPatient = Patient::where('public_id', $request->validated('thera_patient_id'))->lockForUpdate()->first();
                if (! $theraPatient || ! $theraPatient->is_active || ! $theraPatient->booking_enabled || ! $theraPatient->phone_verified_at) {
                    throw ValidationException::withMessages(['thera_patient_id' => 'المريض غير متاح للحجز من دليل ثيرا. ابحث عنه مجددًا.']);
                }
                $patient = DoctorPatient::firstOrCreate(['doctor_id' => $doctor->id, 'patient_id' => $theraPatient->id], ['name' => $theraPatient->name, 'phone' => $theraPatient->phone]);
            } elseif ($request->validated('patient_mode') === 'new_thera') {
                $theraPatient = $this->registerTheraPatient($doctor, $request);
                $patient = DoctorPatient::create(['doctor_id' => $doctor->id, 'patient_id' => $theraPatient->id, 'name' => $theraPatient->name, 'phone' => $theraPatient->phone]);
            } else {
                $patient = $newPatient
                    ? DoctorPatient::create(['doctor_id' => $doctor->id, 'name' => $request->validated('patient_name'), 'phone' => $request->validated('patient_phone')])
                    : DoctorPatient::whereBelongsTo($doctor)->findOrFail($request->integer('doctor_patient_id'));
            }
            $appointment = DoctorAppointment::query()->create([
                ...$request->safe()->only(['notes', 'request_key']),
                'doctor_patient_id' => $patient->id, 'patient_name' => $theraPatient?->name ?? $patient->name, 'patient_phone' => $theraPatient?->phone ?? $patient->phone,
                ...$this->serviceSnapshot($service), 'doctor_id' => $doctor->id, 'starts_at' => $start, 'ends_at' => $start->addMinutes($service->duration_minutes),
                'status' => 'confirmed', 'source' => 'doctor', 'revision' => 1, 'request_fingerprint' => $fingerprint,
            ]);
            $this->record($appointment, 'created');
            $patient->events()->create(['doctor_id' => $doctor->id, 'doctor_appointment_id' => $appointment->id, 'action' => $newPatient ? 'created_with_booking' : 'appointment_added', 'snapshot' => ['appointment_id' => $appointment->id]]);
            if ($theraPatient) {
                PatientDirectoryEvent::create(['doctor_id' => $doctor->id, 'patient_id' => $theraPatient->id, 'doctor_appointment_id' => $appointment->id, 'action' => 'appointment_booked']);
            }

            return $appointment;
        });

        $message = $request->validated('patient_mode') === 'new_thera'
            ? 'تم إنشاء حساب المريض على شبكة ثيرا وتسجيل الموعد. رسالة بيانات الدخول بانتظار الإرسال إلى بريد المريض. لم يُسجل أي دفع أو تحصيل.'
            : 'تم تسجيل الموعد بنجاح. لم يُسجل أي دفع أو تحصيل.';

        return $this->saved($request, $appointment, $message);
    }

    private function registerTheraPatient(Doctor $doctor, SaveDoctorAppointmentRequest $request): Patient
    {
        $email = $request->validated('patient_email');
        $duplicateMessage = 'تعذر إنشاء حساب جديد بهذا البريد. استخدم خيار مرضى ثيرا للمريض المسجل؛ لن تتغير بيانات دخوله.';
        if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages(['patient_email' => $duplicateMessage]);
        }
        $rateKey = 'doctor-network-patient:'.$doctor->id;
        abort_if(RateLimiter::tooManyAttempts($rateKey, 10), 429, 'محاولات إنشاء حسابات كثيرة. حاول بعد دقيقة.');
        RateLimiter::hit($rateKey, 60);
        $password = Str::password(20, symbols: false);
        try {
            $user = User::create(['name' => $request->validated('patient_name'), 'email' => $email, 'password' => $password]);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['patient_email' => $duplicateMessage]);
        }
        $patient = Patient::create(['user_id' => $user->id, 'name' => $user->name, 'phone' => $request->validated('patient_phone'), 'is_active' => true, 'booking_enabled' => false]);
        $user->notify(new PatientAccountCreated($password, $patient->reference()));
        PatientDirectoryEvent::create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id, 'action' => 'account_created']);

        return $patient;
    }

    public function update(SaveDoctorAppointmentRequest $request, DoctorAppointment $doctorAppointment): JsonResponse|RedirectResponse
    {
        DB::transaction(function () use ($request, $doctorAppointment): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorAppointment->refresh();
            $this->editable($doctorAppointment, $request->integer('revision'));
            if ($doctorAppointment->starts_at->lessThanOrEqualTo(CarbonImmutable::now('UTC')) || $doctorAppointment->arrived_at) {
                throw ValidationException::withMessages(['revision' => 'لا يمكن إعادة جدولة موعد بدأ وقته أو سُجل حضور المريض له.']);
            }
            $service = $this->ownService($doctor, $request->integer('doctor_service_id'));
            $sameService = $doctorAppointment->doctor_service_id === $service->id;
            if (! $sameService && $doctorAppointment->payments()->exists()) {
                throw ValidationException::withMessages(['doctor_service_id' => 'لا يمكن تغيير الخدمة أو المنشأة لموعد له قيود مالية؛ راجع التحصيل أولًا.']);
            }
            if ($sameService) {
                $service->duration_minutes = $doctorAppointment->duration_minutes;
            }
            $start = $this->availableStart($service, $request->validated('date'), $request->validated('time'), $doctorAppointment->id);
            $before = $doctorAppointment->auditSnapshot();
            $doctorAppointment->update([
                ...($sameService ? [] : $this->serviceSnapshot($service)), 'starts_at' => $start,
                'ends_at' => $start->addMinutes($service->duration_minutes), 'revision' => $doctorAppointment->revision + 1,
            ]);
            $this->record($doctorAppointment, 'rescheduled', $before, $request->validated('reason'));
        });

        return $this->saved($request, $doctorAppointment, 'تمت إعادة الجدولة وحُفظ الموعد السابق في سجل التغييرات.');
    }

    public function status(Request $request, DoctorAppointment $doctorAppointment): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorAppointment);
        $data = $request->validate(['action' => ['required', Rule::in(['cancelled', 'arrived', 'no_show'])], 'revision' => ['required', 'integer', 'min:1'], 'reason' => ['required_if:action,cancelled,no_show', 'nullable', 'string', 'max:500']]);
        DB::transaction(function () use ($request, $doctorAppointment, $data): void {
            Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorAppointment->refresh();
            $this->editable($doctorAppointment, (int) $data['revision']);
            $membership = $doctorAppointment->doctorFacility;
            if ($membership->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $membership->facility->is_active) {
                throw ValidationException::withMessages(['action' => 'تعديل الموعد يتطلب عضوية نشطة في منشأة نشطة.']);
            }
            $now = CarbonImmutable::now('UTC');
            $action = $data['action'];
            if ($action === 'arrived' && ($doctorAppointment->arrived_at || ! $doctorAppointment->starts_at->setTimezone(config('thera.schedule_timezone'))->isSameDay($now->setTimezone(config('thera.schedule_timezone'))))) {
                throw ValidationException::withMessages(['action' => 'يسجل الحضور مرة واحدة، وفي يوم الموعد فقط.']);
            }
            if ($action === 'no_show' && ($doctorAppointment->ends_at->greaterThan($now) || $doctorAppointment->arrived_at)) {
                throw ValidationException::withMessages(['action' => 'لا يسجل عدم الحضور قبل نهاية الموعد أو لمريض سُجل حضوره.']);
            }
            $before = $doctorAppointment->auditSnapshot();
            $doctorAppointment->update([...($action === 'arrived' ? ['arrived_at' => $now] : ['status' => $action]), 'revision' => $doctorAppointment->revision + 1]);
            $this->record($doctorAppointment, $action, $before, $data['reason'] ?? null);
        });

        return $this->saved($request, $doctorAppointment, 'تم تحديث الموعد. حالة الدفع والزيارة لم تتغير.');
    }

    private function ownService(Doctor $doctor, int $id): DoctorService
    {
        return DoctorService::query()->whereHas('doctorFacility', fn ($query) => $query->whereBelongsTo($doctor))->with(['doctorFacility.facility', 'doctorFacility.doctor', 'doctorFacility.schedule.exceptions'])->findOrFail($id);
    }

    private function availableStart(DoctorService $service, string $date, string $time, ?int $ignore = null): CarbonImmutable
    {
        $day = CarbonImmutable::parse($date, config('thera.schedule_timezone'));
        $preview = $service->doctorFacility->schedule?->preview($day, $service, $ignore);
        if (! $preview || ! in_array($time, $preview['slots'], true)) {
            throw ValidationException::withMessages(['time' => 'الوقت المختار غير متاح. حدّث الأوقات واختر فترة أخرى. '.($preview['note'] ?? 'لا يوجد جدول محفوظ لهذه المنشأة.')]);
        }
        $start = CarbonImmutable::parse($date.' '.$time, config('thera.schedule_timezone'));
        if ($start->format('Y-m-d H:i') !== $date.' '.$time) {
            throw ValidationException::withMessages(['time' => 'هذا الوقت غير صالح في المنطقة الزمنية للجدول.']);
        }

        return $start->utc();
    }

    /** @return array<string, mixed> */
    private function serviceSnapshot(DoctorService $service): array
    {
        return ['doctor_service_id' => $service->id, 'doctor_facility_id' => $service->doctor_facility_id, 'service_name' => $service->name, 'price_minor' => $service->price_minor, 'currency' => $service->currency, 'duration_minutes' => $service->duration_minutes];
    }

    private function editable(DoctorAppointment $appointment, int $revision): void
    {
        if ($appointment->revision !== $revision || $appointment->status !== 'confirmed' || $appointment->visit()->exists()) {
            throw ValidationException::withMessages(['revision' => 'تغيّر الموعد أو أُغلق. أعد فتح تفاصيله قبل تنفيذ عملية جديدة.']);
        }
    }

    /** @param array<string, mixed>|null $before */
    private function record(DoctorAppointment $appointment, string $action, ?array $before = null, ?string $reason = null): void
    {
        $appointment->events()->create(['doctor_id' => $appointment->doctor_id, 'action' => $action, 'reason' => $reason, 'before' => $before, 'after' => $appointment->auditSnapshot()]);
    }

    private function saved(Request $request, DoctorAppointment $appointment, string $message): JsonResponse|RedirectResponse
    {
        $url = route('doctor.appointments.show', ['appointment' => $appointment->id]);

        return $request->expectsJson() ? response()->json(['message' => $message, 'url' => $url]) : redirect($url)->with('status', $message);
    }
}
