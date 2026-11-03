<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorPatient;
use App\Models\DoctorService;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\PatientFavorite;
use App\Models\PatientPaymentAttempt;
use App\Models\Specialty;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PatientBookingController extends Controller
{
    private function patient(Request $request): Patient
    {
        return Patient::accessibleTo($request->user())->where('public_id', $request->route('patient'))->firstOrFail();
    }

    private function simulation(): bool
    {
        return app()->environment(['local', 'testing', 'staging']) && config('patient.development_payments');
    }

    private function appointment(Request $request, Patient $patient): DoctorAppointment
    {
        return DoctorAppointment::whereHas('patient', fn (Builder $q) => $q->where('patient_id', $patient->id))->with(['doctor', 'doctorFacility.facility', 'visit', 'queueEntry'])->withSum('payments as paid_minor', 'amount_minor')->findOrFail($request->route('appointment'));
    }

    public function doctors(Request $request): JsonResponse
    {
        $data = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'city' => ['nullable', 'string', 'max:100'], 'specialty_id' => ['nullable', 'integer'], 'favorite_only' => ['nullable', 'boolean']]);
        $services = DoctorService::bookingEnabled()->when($data['specialty_id'] ?? null, fn ($q, $id) => $q->whereHas('doctorFacility.doctor.specialtyAssignments', fn ($q) => $q->where('specialty_id', $id)->where('verification_status', 'verified')))->when($data['favorite_only'] ?? false, fn ($q) => $q->whereHas('doctorFacility', fn ($q) => $q->whereIn('doctor_id', PatientFavorite::where('user_id', $request->user()->id)->select('doctor_id'))))->with(['doctorFacility.doctor' => fn ($q) => $q->withAvg(['patientReviews as patient_rating' => fn ($r) => $r->where('status', 'approved')], 'rating')->withCount(['patientReviews as published_reviews_count' => fn ($r) => $r->where('status', 'approved')]), 'doctorFacility.facility'])->when($data['q'] ?? null, fn (Builder $q, string $search) => $q->where(fn (Builder $q) => $q->where('name', 'like', '%'.$search.'%')->orWhereHas('doctorFacility.doctor', fn (Builder $q) => $q->where('name', 'like', '%'.$search.'%'))))->when($data['city'] ?? null, fn (Builder $q, string $city) => $q->whereHas('doctorFacility.facility', fn (Builder $q) => $q->where('city', $city)))->orderBy('id')->paginate(20);

        return response()->json(['data' => $services->getCollection()->map(fn ($service) => ['id' => $service->id, 'name' => $service->name, 'description' => $service->description, 'price_minor' => $service->price_minor, 'currency' => $service->currency, 'duration_minutes' => $service->duration_minutes, 'instructions' => $service->instructions, 'doctor' => $service->doctorFacility->doctor->only('id', 'name', 'professional_title', 'professional_bio', 'years_experience', 'patient_rating', 'published_reviews_count'), 'facility' => $service->doctorFacility->facility->only('id', 'name', 'city', 'type'), 'payment_policy' => $service->doctorFacility->doctor->accountPreferences()['payment_policy']]), 'meta' => ['current_page' => $services->currentPage(), 'last_page' => $services->lastPage()]])->header('Cache-Control', 'private, no-store');
    }

    public function filters(): JsonResponse
    {
        return response()->json(['data' => ['cities' => Facility::where('is_active', true)->where('bookings_enabled', true)->whereHas('doctorMemberships.services', fn ($q) => $q->bookingEnabled())->orderBy('city')->pluck('city')->unique()->values(), 'specialties' => Specialty::where('is_active', true)->whereHas('doctorAssignments', fn ($q) => $q->where('verification_status', 'verified'))->orderBy('name_ar')->get(['id', 'name_ar', 'name_en'])]]);
    }

    public function availability(Request $request, int $service): JsonResponse
    {
        $data = $request->validate(['date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now(config('thera.schedule_timezone'))->addYear()->toDateString()]]);
        $record = DoctorService::bookingEnabled()->with(['doctorFacility.schedule.exceptions', 'doctorFacility.doctor', 'doctorFacility.facility'])->findOrFail($service);

        return response()->json(['data' => $record->doctorFacility->schedule?->preview(CarbonImmutable::parse($data['date'], config('thera.schedule_timezone')), $record) ?? ['slots' => [], 'note' => 'لا يوجد جدول متاح.']]);
    }

    private function requiredMinor(array $policy, int $price): int
    {
        return match ($policy['type']) {
            'full' => $price,
            'percentage' => intdiv($price * (int) $policy['deposit_percent'] + 99, 100),
            default => 0,
        };
    }

    private function serialize(DoctorAppointment $appointment): array
    {
        $appointment->loadMissing(['doctor', 'doctorFacility.facility', 'visit', 'queueEntry'])->loadSum('payments as paid_minor', 'amount_minor');
        $expired = $appointment->status === 'pending_payment' && $appointment->hold_expires_at?->isPast();
        $policy = $appointment->payment_policy_snapshot;

        return [...$appointment->only('id', 'doctor_service_id', 'revision', 'source', 'is_test', 'patient_name', 'service_name', 'price_minor', 'currency', 'starts_at', 'ends_at', 'hold_expires_at'), 'status' => $expired ? 'expired' : $appointment->status, 'doctor' => $appointment->doctor->only('id', 'name'), 'facility' => $appointment->doctorFacility->facility->only('id', 'name', 'city'), 'payment_policy' => $policy, 'required_minor' => $policy ? $this->requiredMinor($policy, $appointment->price_minor) : 0, 'paid_minor' => $appointment->paidMinor(), 'remaining_minor' => $appointment->remainingMinor(), 'payment_status' => $appointment->paymentStatus(), 'queue_state' => $appointment->queueState(), 'ahead' => $this->ahead($appointment), 'can_edit' => $appointment->status === 'confirmed' && $appointment->starts_at->isFuture() && ! $appointment->arrived_at && ! $appointment->visit];
    }

    private function ahead(DoctorAppointment $appointment): ?int
    {
        if (! in_array($appointment->queueState(), ['ready', 'called', 'in_visit'], true)) {
            return null;
        }
        $day = $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->startOfDay();

        return DoctorAppointment::where('doctor_id', $appointment->doctor_id)->where('doctor_facility_id', $appointment->doctor_facility_id)->where('status', 'confirmed')->whereNotNull('arrived_at')
            ->where('starts_at', '>=', $day->utc())->where('starts_at', '<', $day->addDay()->utc())
            ->where(fn (Builder $q) => $q->where('starts_at', '<', $appointment->starts_at)->orWhere(fn (Builder $q) => $q->where('starts_at', $appointment->starts_at)->where('id', '<', $appointment->id)))
            ->with(['doctor', 'visit', 'queueEntry'])->withSum('payments as paid_minor', 'amount_minor')->get()->filter(fn ($a) => in_array($a->queueState(), ['ready', 'called', 'in_visit'], true))->count();
    }

    public function checkIn(Request $request): JsonResponse
    {
        $record = $this->appointment($request, $this->patient($request));
        $data = $request->validate(['code' => ['required', 'string', 'max:300']]);
        DB::transaction(function () use ($record, $data): void {
            Doctor::lockForUpdate()->findOrFail($record->doctor_id);
            $record->refresh()->load('doctorFacility.facility');
            abort_unless(hash_equals($record->doctorFacility->facility->checkInCode(), $data['code']), 422, 'رمز المنشأة غير صحيح لهذا الموعد.');
            abort_unless($record->status === 'confirmed' && $record->starts_at->setTimezone(config('thera.schedule_timezone'))->isToday() && $record->doctorFacility->membership_status === 'active' && $record->doctorFacility->facility->is_active, 409, 'الوصول متاح لموعد مؤكد في منشأة نشطة خلال يومه فقط.');
            if ($record->arrived_at) {
                return;
            }
            $before = $record->auditSnapshot();
            $record->update(['arrived_at' => now(), 'revision' => $record->revision + 1]);
            $record->events()->create(['doctor_id' => $record->doctor_id, 'action' => 'arrived', 'before' => $before, 'after' => $record->auditSnapshot()]);
        });

        return response()->json(['data' => $this->serialize($record->fresh())]);
    }

    public function index(Request $request): JsonResponse
    {
        $patient = $this->patient($request);
        $records = DoctorAppointment::whereHas('patient', fn (Builder $q) => $q->where('patient_id', $patient->id))->with(['doctor', 'doctorFacility.facility', 'visit', 'queueEntry'])->orderByDesc('starts_at')->orderByDesc('id')->paginate(20);

        return response()->json(['data' => $records->getCollection()->map(fn ($a) => $this->serialize($a)), 'meta' => ['last_page' => $records->lastPage(), 'current_page' => $records->currentPage()]])->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->serialize($this->appointment($request, $this->patient($request)))])->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request): JsonResponse
    {
        $patient = $this->patient($request);
        abort_unless($patient->booking_enabled && $patient->phone_verified_at, 403, 'وثق الهاتف قبل الحجز.');
        $data = $request->validate(['doctor_service_id' => ['required', 'integer'], 'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now(config('thera.schedule_timezone'))->addYear()->toDateString()], 'time' => ['required', 'date_format:H:i'], 'request_key' => ['required', 'uuid'], 'notes' => ['nullable', 'string', 'max:500']]);
        $service = DoctorService::bookingEnabled()->with(['doctorFacility.doctor', 'doctorFacility.facility', 'doctorFacility.schedule.exceptions'])->findOrFail($data['doctor_service_id']);
        $appointment = DB::transaction(function () use ($patient, $data, $service): DoctorAppointment {
            $doctor = Doctor::lockForUpdate()->findOrFail($service->doctorFacility->doctor_id);
            $fingerprint = hash('sha256', json_encode([$patient->public_id, $data['doctor_service_id'], $data['date'], $data['time'], $data['notes'] ?? ''], JSON_THROW_ON_ERROR));
            $existing = DoctorAppointment::where('doctor_id', $doctor->id)->where('request_key', $data['request_key'])->first();
            if ($existing) {
                abort_unless(hash_equals($existing->request_fingerprint, $fingerprint), 409, 'استُخدم مفتاح الحجز لبيانات أخرى.');

                return $existing;
            }
            $preview = $service->doctorFacility->schedule?->preview(CarbonImmutable::parse($data['date'], config('thera.schedule_timezone')), $service);
            if (! in_array($data['time'], $preview['slots'] ?? [], true)) {
                throw ValidationException::withMessages(['time' => 'الوقت غير متاح. حدّث الأوقات واختر فترة أخرى.']);
            }
            $policy = $doctor->accountPreferences()['payment_policy'];
            $required = $this->requiredMinor($policy, $service->price_minor);
            abort_if($required > 0 && ! $this->simulation(), 503, 'بوابة الدفع الحقيقي غير مفعلة.');
            $file = DoctorPatient::firstOrCreate(['doctor_id' => $doctor->id, 'patient_id' => $patient->id], ['name' => $patient->name, 'phone' => $patient->phone]);
            $start = CarbonImmutable::parse($data['date'].' '.$data['time'], config('thera.schedule_timezone'))->utc();
            $appointment = new DoctorAppointment(['doctor_id' => $doctor->id, 'doctor_patient_id' => $file->id, 'doctor_facility_id' => $service->doctor_facility_id, 'doctor_service_id' => $service->id, 'patient_name' => $patient->name, 'patient_phone' => $patient->phone, 'service_name' => $service->name, 'price_minor' => $service->price_minor, 'currency' => $service->currency, 'duration_minutes' => $service->duration_minutes, 'starts_at' => $start, 'ends_at' => $start->addMinutes($service->duration_minutes), 'status' => $required > 0 ? 'pending_payment' : 'confirmed', 'source' => DoctorAppointment::SOURCE_PATIENT_APP, 'revision' => 1, 'request_key' => $data['request_key'], 'request_fingerprint' => $fingerprint, 'notes' => $data['notes'] ?? null]);
            $appointment->forceFill(['is_test' => $this->simulation(), 'payment_policy_snapshot' => $policy, 'hold_expires_at' => $required > 0 ? now()->addMinutes(10) : null])->save();
            $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'created', 'after' => $appointment->auditSnapshot()]);

            return $appointment;
        });

        return response()->json(['data' => $this->serialize($appointment)]);
    }

    public function pay(Request $request): JsonResponse
    {
        abort_unless($this->simulation(), 403, 'محاكاة الدفع متاحة للتطوير فقط.');
        $record = $this->appointment($request, $this->patient($request));
        $data = $request->validate(['request_key' => ['required', 'uuid'], 'outcome' => ['required', 'in:succeeded,failed,pending'], 'pay_full' => ['sometimes', 'boolean']]);
        $attempt = DB::transaction(function () use ($request, $record, $data): PatientPaymentAttempt {
            Doctor::lockForUpdate()->findOrFail($record->doctor_id);
            $record->refresh()->loadSum('payments as paid_minor', 'amount_minor');
            abort_unless($record->is_test, 409);
            $existing = PatientPaymentAttempt::where('doctor_appointment_id', $record->id)->where('request_key', $data['request_key'])->first();
            if ($existing) {
                $requestedOutcome = $data['outcome'] === 'pending' ? 'succeeded' : $data['outcome'];
                abort_unless($existing->user_id === $request->user()->id && $existing->outcome === $requestedOutcome, 409, 'استُخدم مفتاح الدفع لمحاولة مختلفة.');

                return $existing;
            }
            abort_unless((in_array($record->status, ['pending_payment', 'confirmed'], true) || ($record->status === 'completed' && ($record->payment_policy_snapshot['remaining_timing'] ?? null) === 'after_visit')) && ! ($record->status === 'pending_payment' && $record->hold_expires_at->isPast()), 409, 'الحجز غير صالح للدفع.');
            abort_if(PatientPaymentAttempt::where('doctor_appointment_id', $record->id)->where('status', 'pending')->exists(), 409, 'تحقق من محاولة الدفع السابقة أولًا.');
            $policy = $record->payment_policy_snapshot;
            $required = max(0, $this->requiredMinor($policy, $record->price_minor) - $record->paidMinor());
            $full = $data['pay_full'] ?? false;
            abort_if($full && ! $policy['allow_early_payment'] && ! $record->arrived_at && $required < $record->remainingMinor(), 422, 'الدفع المبكر غير مسموح.');
            $due = ($record->arrived_at && $policy['remaining_timing'] !== 'after_visit') || $record->status === 'completed';
            $amount = $full ? $record->remainingMinor() : ($required > 0 ? $required : ($due ? $record->remainingMinor() : 0));
            abort_if($amount <= 0, 422, 'لا يوجد مبلغ مستحق لهذه المحاولة.');
            $attempt = PatientPaymentAttempt::create(['doctor_appointment_id' => $record->id, 'user_id' => $request->user()->id, 'request_key' => $data['request_key'], 'amount_minor' => $amount, 'status' => $data['outcome'], 'outcome' => $data['outcome'] === 'pending' ? 'succeeded' : $data['outcome']]);
            if ($attempt->status === 'succeeded') {
                $record->forceFill(['test_paid_minor' => $record->test_paid_minor + $amount, 'status' => $record->status === 'pending_payment' ? 'confirmed' : $record->status, 'hold_expires_at' => null, 'revision' => $record->revision + 1])->save();
            }

            return $attempt;
        });

        return response()->json(['data' => ['attempt' => $attempt->only('id', 'status', 'amount_minor'), 'appointment' => $this->serialize($record->fresh())]]);
    }

    public function resolve(Request $request): JsonResponse
    {
        abort_unless($this->simulation(), 403);
        $record = $this->appointment($request, $this->patient($request));
        DB::transaction(function () use ($record): void {
            Doctor::lockForUpdate()->findOrFail($record->doctor_id);
            $record->refresh();
            $attempt = PatientPaymentAttempt::where('doctor_appointment_id', $record->id)->where('status', 'pending')->lockForUpdate()->first();
            if (! $attempt) {
                return;
            }
            if (! in_array($record->status, ['pending_payment', 'confirmed', 'completed'], true) || ($record->hold_expires_at && $record->hold_expires_at->isPast())) {
                $attempt->update(['status' => 'failed']);

                return;
            }
            $attempt->update(['status' => $attempt->outcome]);
            if ($attempt->status === 'succeeded') {
                $record->forceFill(['test_paid_minor' => $record->test_paid_minor + $attempt->amount_minor, 'status' => $record->status === 'pending_payment' ? 'confirmed' : $record->status, 'hold_expires_at' => null, 'revision' => $record->revision + 1])->save();
            }
        });

        return response()->json(['data' => $this->serialize($record->fresh())]);
    }

    public function change(Request $request): JsonResponse
    {
        $record = $this->appointment($request, $this->patient($request));
        $data = $request->validate(['action' => ['required', 'in:cancel,reschedule'], 'revision' => ['required', 'integer'], 'reason' => ['required', 'string', 'max:500'], 'date' => ['required_if:action,reschedule', 'date_format:Y-m-d', 'after_or_equal:today'], 'time' => ['required_if:action,reschedule', 'date_format:H:i']]);
        DB::transaction(function () use ($record, $data): void {
            Doctor::lockForUpdate()->findOrFail($record->doctor_id);
            $record->refresh();
            abort_unless($record->revision === (int) $data['revision'] && in_array($record->status, ['confirmed', 'pending_payment'], true) && $record->starts_at->isFuture() && ! $record->arrived_at && ! $record->visit()->exists(), 409, 'لم يعد تعديل الموعد متاحًا.');
            $before = $record->auditSnapshot();
            if ($data['action'] === 'cancel') {
                $record->forceFill(['status' => 'cancelled', 'test_paid_minor' => 0, 'revision' => $record->revision + 1])->save();
                PatientPaymentAttempt::where('doctor_appointment_id', $record->id)->whereIn('status', ['pending', 'succeeded'])->update(['status' => 'reversed']);
            } else {
                abort_unless($record->status === 'confirmed', 409);
                $service = $record->doctorService;
                $service->duration_minutes = $record->duration_minutes;
                $preview = $service->doctorFacility->schedule?->preview(CarbonImmutable::parse($data['date'], config('thera.schedule_timezone')), $service, $record->id);
                abort_unless(in_array($data['time'], $preview['slots'] ?? [], true), 422, 'الوقت غير متاح.');
                $start = CarbonImmutable::parse($data['date'].' '.$data['time'], config('thera.schedule_timezone'))->utc();
                $record->update(['starts_at' => $start, 'ends_at' => $start->addMinutes($record->duration_minutes), 'revision' => $record->revision + 1]);
            }
            $record->events()->create(['doctor_id' => $record->doctor_id, 'action' => $data['action'] === 'cancel' ? 'cancelled' : 'rescheduled', 'before' => $before, 'after' => $record->auditSnapshot(), 'reason' => $data['reason']]);
        });

        return response()->json(['data' => $this->serialize($record->fresh())]);
    }
}
