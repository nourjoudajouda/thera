<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorFacility;
use App\Models\DoctorPrescription;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DoctorVisitController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate(['status' => ['nullable', Rule::in(array_keys(DoctorVisit::STATUSES))], 'search' => ['nullable', 'string', 'max:100'], 'tab' => ['nullable', Rule::in(['all', 'active', 'completed'])], 'date' => ['nullable', 'date_format:Y-m-d'], 'sort' => ['nullable', Rule::in(['newest', 'oldest'])]]);
        $doctor = $request->user('doctor');
        $base = DoctorVisit::query()->whereBelongsTo($doctor);
        $today = CarbonImmutable::today(config('thera.schedule_timezone'));
        $counts = ['all' => (clone $base)->count(), 'active' => (clone $base)->whereNull('closed_at')->count(), 'completed' => (clone $base)->whereNotNull('closed_at')->count(), 'today' => (clone $base)->where('started_at', '>=', $today->utc())->where('started_at', '<', $today->addDay()->utc())->count(), 'completed_today' => (clone $base)->where('closed_at', '>=', $today->utc())->where('closed_at', '<', $today->addDay()->utc())->count()];
        $tab = $filters['tab'] ?? 'all';
        $visits = $base->with('doctorAppointment.doctorFacility.facility')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $filters['status']))
            ->when($tab === 'active', fn ($query) => $query->whereNull('closed_at'))
            ->when($tab === 'completed', fn ($query) => $query->whereNotNull('closed_at'))
            ->when($request->filled('date'), function ($query) use ($filters): void {
                $day = CarbonImmutable::parse($filters['date'], config('thera.schedule_timezone'));
                $query->where('started_at', '>=', $day->utc())->where('started_at', '<', $day->addDay()->utc());
            })
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->whereHas('doctorAppointment', fn ($query) => $query->where('patient_name', 'like', '%'.$filters['search'].'%')->orWhere('id', $filters['search']))->orWhere('id', $filters['search'])))
            ->orderBy('updated_at', ($filters['sort'] ?? 'newest') === 'oldest' ? 'asc' : 'desc')->orderBy('id', ($filters['sort'] ?? 'newest') === 'oldest' ? 'asc' : 'desc')->paginate(20)->withQueryString();

        return response()->view('doctor.clinical.visits-workspace', compact('doctor', 'visits', 'counts', 'tab'))->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request): Response|RedirectResponse
    {
        $request->validate(['visit' => ['nullable', 'integer', 'min:1']]);
        if (! $request->filled('visit')) {
            return redirect()->route('doctor.clinical.visits');
        }
        $visit = DoctorVisit::with(['doctorAppointment.doctorFacility.facility', 'prescription', 'diagnosticOrders:id,doctor_visit_id,kind,status,approved_at,document_version,corrects_document_id', 'diagnosticOrders.correction:id,corrects_document_id,approved_at', 'referrals:id,doctor_visit_id,kind,status,approved_at,document_version,corrects_document_id', 'referrals.correction:id,corrects_document_id,approved_at', 'revisions' => fn ($query) => $query->where('action', '!=', 'viewed')->select(['id', 'doctor_visit_id', 'action', 'revision', 'created_at'])])->findOrFail($request->integer('visit'));
        Gate::forUser($request->user('doctor'))->authorize('view', $visit);
        $patientHistory = $this->patientHistory($request->user('doctor'), $visit);
        $sharedVisitIds = $patientHistory['histories']->concat($patientHistory['diagnoses'])->concat($patientHistory['visits'])
            ->concat($patientHistory['prescriptions']->pluck('doctorVisit'))->concat($patientHistory['reports']->pluck('order.doctorVisit'))
            ->where('doctor_id', '!=', $request->user('doctor')->id)->pluck('id')->unique()->values()->all();
        $visit->revisions()->create(['doctor_id' => $request->user('doctor')->id, 'action' => 'viewed', 'revision' => $visit->revision,
            'snapshot' => $sharedVisitIds ? ['shared_history_visit_ids' => $sharedVisitIds] : null]);

        return response()->view('doctor.clinical.visit-workspace', ['doctor' => $request->user('doctor'), 'visit' => $visit, 'appointment' => $visit->doctorAppointment, 'patientHistory' => $patientHistory])->header('Cache-Control', 'private, no-store');
    }

    /** @return array{linked: bool, shared: bool, patient_id: ?int, histories: Collection, diagnoses: Collection, visits: Collection, prescriptions: Collection, reports: Collection} */
    private function patientHistory(Doctor $doctor, DoctorVisit $visit): array
    {
        $history = ['linked' => false, 'shared' => false, 'patient_id' => null, 'histories' => collect(), 'diagnoses' => collect(), 'visits' => collect(), 'prescriptions' => collect(), 'reports' => collect()];
        $patient = $visit->doctorAppointment->patient;
        if (! $patient || $patient->doctor_id !== $doctor->id) {
            return $history;
        }
        $appointment = $visit->doctorAppointment;
        $shared = $patient->patient_id !== null && $visit->closed_at === null
            && $appointment->status === 'confirmed' && $appointment->arrived_at !== null
            && $doctor->verification_status === 'verified'
            && $appointment->doctorFacility->doctor_id === $doctor->id
            && $appointment->doctorFacility->membership_status === DoctorFacility::STATUS_ACTIVE
            && $appointment->doctorFacility->facility->is_active
            && $patient->theraPatient?->hasDoctorConsent($doctor);
        $prior = DoctorVisit::query()
            ->where(function (Builder $query) use ($doctor, $patient, $shared): void {
                $query->where(fn (Builder $owned) => $owned->whereBelongsTo($doctor)
                    ->whereHas('doctorAppointment', fn (Builder $appointments) => $appointments->where('doctor_id', $doctor->id)->where('doctor_patient_id', $patient->id)));
                if ($shared) {
                    $query->orWhere(fn (Builder $network) => $network->whereNotNull('closed_at')->where('status', 'completed')
                        ->whereHas('doctorAppointment', fn (Builder $appointments) => $appointments
                            ->whereColumn('doctor_appointments.doctor_id', 'doctor_visits.doctor_id')
                            ->whereHas('patient', fn (Builder $patients) => $patients->where('patient_id', $patient->patient_id)
                                ->whereColumn('doctor_patients.doctor_id', 'doctor_appointments.doctor_id'))));
                }
            })
            ->where(fn ($query) => $query->where('started_at', '<', $visit->started_at)->orWhere(fn ($query) => $query->where('started_at', $visit->started_at)->where('id', '<', $visit->id)));
        $visits = (clone $prior)->with(['doctor:id,name', 'doctorAppointment.doctorFacility.facility'])->orderByDesc('started_at')->orderByDesc('id');
        $documented = (clone $visits)->whereNotNull('closed_at');
        $prescriptions = DoctorPrescription::query()->whereIn('doctor_visit_id', (clone $prior)->select('id'))
            ->whereHas('doctorVisit', fn (Builder $query) => $query->whereColumn('doctor_visits.doctor_id', 'doctor_prescriptions.doctor_id'))
            ->where('status', 'approved')->whereNotNull('approved_at')->whereDoesntHave('correction', fn ($query) => $query->whereNotNull('approved_at'))
            ->with(['doctorVisit.doctor:id,name', 'doctorVisit.doctorAppointment.doctorFacility.facility'])->orderByDesc('approved_at')->orderByDesc('id')->limit(3)->get();
        $orders = DoctorDiagnosticOrder::query()->whereIn('doctor_visit_id', (clone $prior)->select('id'))
            ->whereHas('doctorVisit', fn (Builder $query) => $query->whereColumn('doctor_visits.doctor_id', 'doctor_diagnostic_orders.doctor_id'))
            ->where('status', 'approved')->whereNotNull('approved_at')->whereDoesntHave('correction', fn ($query) => $query->whereNotNull('approved_at'));
        $reports = DoctorExternalReport::query()->whereIn('doctor_diagnostic_order_id', $orders->select('id'))
            ->whereHas('order', fn (Builder $query) => $query->whereColumn('doctor_diagnostic_orders.doctor_id', 'doctor_external_reports.doctor_id'))
            ->with(['order.doctorVisit.doctor:id,name', 'order.doctorVisit.doctorAppointment.doctorFacility.facility'])->orderByDesc('report_date')->orderByDesc('id')->limit(3)->get();

        return array_replace($history, ['linked' => true, 'shared' => $shared, 'patient_id' => $patient->id,
            'histories' => (clone $documented)->whereNotNull('content->medical_history')->where('content->medical_history', '!=', '')->limit(3)->get(),
            'diagnoses' => (clone $documented)->whereNotNull('content->diagnosis')->where('content->diagnosis', '!=', '')->limit(3)->get(),
            'visits' => $visits->limit(3)->get(), 'prescriptions' => $prescriptions, 'reports' => $reports,
        ]);
    }

    public function store(Request $request, DoctorAppointment $doctorAppointment): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorAppointment);
        $request->validate(['revision' => ['required', 'integer', 'min:1']]);
        $visit = DB::transaction(function () use ($request, $doctorAppointment): DoctorVisit {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorAppointment->refresh()->load(['visit', 'queueEntry', 'doctorFacility.facility'])->loadSum('payments as paid_minor', 'amount_minor');
            $doctorAppointment->setRelation('doctor', $doctor);
            if ($doctorAppointment->visit) {
                return $doctorAppointment->visit;
            }
            $this->activeContext($doctor, $doctorAppointment);
            if ($doctorAppointment->revision !== $request->integer('revision') || $doctorAppointment->queueState() !== 'called') {
                throw ValidationException::withMessages(['revision' => 'بدء الزيارة يتطلب موعدًا مؤكدًا اليوم، وحضورًا واستدعاءً مسجلًا، والسداد الكامل إذا كان اشتراط التحصيل مفعّلًا. حدّث الموعد قبل المحاولة.']);
            }
            $visit = $doctorAppointment->visit()->create(['doctor_id' => $doctor->id, 'doctor_facility_id' => $doctorAppointment->doctor_facility_id, 'status' => 'draft', 'content' => [], 'revision' => 1, 'started_at' => now('UTC')]);
            $visit->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'started', 'revision' => 1, 'snapshot' => ['status' => 'draft', 'content' => []]]);
            $doctorAppointment->update(['revision' => $doctorAppointment->revision + 1]);
            $doctorAppointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'visit_started', 'after' => ['visit_id' => $visit->id, 'revision' => $doctorAppointment->revision]]);

            return $visit;
        }, 3);

        return $this->saved($request, $visit, 'تم فتح الزيارة الطبية.');
    }

    public function update(Request $request, DoctorVisit $doctorVisit): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorVisit);
        $closing = $request->input('intent') === 'close';
        $messages = ['required' => 'هذا الحقل مطلوب لإكمال الزيارة.', 'required_without' => 'إكمال الزيارة يتطلب تشخيصًا أو تقييمًا سريريًا واضحًا.', 'required_with' => 'حدد إن كان التشخيص مبدئيًا أو مؤكدًا.'];
        $rules = ['revision' => ['required', 'integer', 'min:1'], 'intent' => ['required', Rule::in(['save', 'close'])], 'diagnosis_type' => [$closing ? 'required_with:diagnosis' : 'nullable', Rule::in(['initial', 'confirmed'])]];
        foreach (['complaint', 'medical_history', 'physical_exam', 'clinical_notes', 'assessment', 'diagnosis', 'treatment_plan', 'followup_plan'] as $field) {
            $rules[$field] = ['nullable', 'string', 'max:10000'];
            $messages[$field.'.max'] = 'النص أطول من الحد المسموح.';
        }
        if ($closing) {
            $rules['assessment'][] = 'required_without:diagnosis';
            $rules['diagnosis'][] = 'required_without:assessment';
        }
        $rules['vitals'] = ['nullable', 'array:systolic,diastolic,pulse,temperature,weight,height'];
        foreach (['systolic', 'diastolic', 'pulse', 'temperature', 'weight', 'height'] as $field) {
            $rules['vitals.'.$field] = ['nullable', 'numeric', 'min:0', 'max:1000'];
        }
        $data = $request->validate($rules, $messages, ['complaint' => 'الشكوى الرئيسية', 'medical_history' => 'التاريخ المرضي', 'physical_exam' => 'الفحص السريري', 'assessment' => 'التقييم الطبي', 'diagnosis' => 'التشخيص', 'treatment_plan' => 'الخطة العلاجية', 'followup_plan' => 'خطة المتابعة']);
        DB::transaction(function () use ($request, $doctorVisit, $data, $closing): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorVisit->refresh()->load('doctorAppointment.doctorFacility.facility');
            $appointment = $doctorVisit->doctorAppointment;
            $this->activeContext($doctor, $appointment);
            if ($doctorVisit->closed_at || $doctorVisit->revision !== (int) $data['revision'] || $appointment->status !== 'confirmed' || ! $appointment->arrived_at) {
                throw ValidationException::withMessages(['revision' => 'الزيارة مغلقة أو تغيرت بياناتها. أعد فتحها قبل الحفظ.']);
            }
            $content = collect($data)->only(['complaint', 'medical_history', 'physical_exam', 'clinical_notes', 'assessment', 'diagnosis', 'diagnosis_type', 'treatment_plan', 'followup_plan', 'vitals'])->all();
            $doctorVisit->update(['content' => $content, 'status' => $closing ? 'completed' : $doctorVisit->workflowStatus($content), 'closed_at' => $closing ? now('UTC') : null, 'revision' => $doctorVisit->revision + 1]);
            $doctorVisit->revisions()->create(['doctor_id' => $doctor->id, 'action' => $closing ? 'completed' : 'saved', 'revision' => $doctorVisit->revision, 'snapshot' => ['content' => $content, 'status' => $doctorVisit->status]]);
            if ($closing) {
                $before = $appointment->auditSnapshot();
                $appointment->update(['status' => 'completed', 'revision' => $appointment->revision + 1]);
                $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'visit_completed', 'before' => $before, 'after' => $appointment->auditSnapshot()]);
            }
        }, 3);

        return $this->saved($request, $doctorVisit, $closing ? 'تم إغلاق الزيارة وإكمال الموعد. الزيارة الآن للقراءة فقط.' : 'حُفظت مسودة الزيارة على الخادم.');
    }

    private function activeContext(Doctor $doctor, DoctorAppointment $appointment): void
    {
        if ($doctor->verification_status !== 'verified' || $appointment->doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $appointment->doctorFacility->facility->is_active) {
            throw ValidationException::withMessages(['revision' => 'الكتابة السريرية تتطلب طبيبًا موثقًا وعضوية ومنشأة نشطتين.']);
        }
    }

    private function saved(Request $request, DoctorVisit $visit, string $message): JsonResponse|RedirectResponse
    {
        $url = $visit->closed_at ? route('doctor.appointments.index', ['view' => 'list']) : route('doctor.clinical.visit', ['visit' => $visit->id]);
        $redirect = (bool) $visit->closed_at;
        if ($redirect && $request->expectsJson()) {
            $request->session()->flash('status', $message);
        }

        return $request->expectsJson() ? response()->json(compact('message', 'url', 'redirect')) : redirect($url)->with('status', $message);
    }
}
