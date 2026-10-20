<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use App\Models\DoctorVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DoctorVisitController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate(['status' => ['nullable', Rule::in(array_keys(DoctorVisit::STATUSES))], 'search' => ['nullable', 'string', 'max:100']]);
        $doctor = $request->user('doctor');
        $visits = DoctorVisit::query()->whereBelongsTo($doctor)->with('doctorAppointment.doctorFacility.facility')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $filters['status']))
            ->when($request->filled('search'), fn ($query) => $query->whereHas('doctorAppointment', fn ($query) => $query->where('patient_name', 'like', '%'.$filters['search'].'%')))
            ->latest('started_at')->paginate(20)->withQueryString();

        return response()->view('doctor.clinical.visits-workspace', compact('doctor', 'visits'))->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request): Response|RedirectResponse
    {
        $request->validate(['visit' => ['nullable', 'integer', 'min:1']]);
        if (! $request->filled('visit')) {
            return redirect()->route('doctor.clinical.visits');
        }
        $visit = DoctorVisit::with(['doctorAppointment.doctorFacility.facility', 'prescription', 'revisions' => fn ($query) => $query->where('action', '!=', 'viewed')->select(['id', 'doctor_visit_id', 'action', 'revision', 'created_at'])])->findOrFail($request->integer('visit'));
        Gate::forUser($request->user('doctor'))->authorize('view', $visit);
        $visit->revisions()->create(['doctor_id' => $request->user('doctor')->id, 'action' => 'viewed', 'revision' => $visit->revision]);

        return response()->view('doctor.clinical.visit-workspace', ['doctor' => $request->user('doctor'), 'visit' => $visit, 'appointment' => $visit->doctorAppointment])->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request, DoctorAppointment $doctorAppointment): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorAppointment);
        $request->validate(['revision' => ['required', 'integer', 'min:1']]);
        $visit = DB::transaction(function () use ($request, $doctorAppointment): DoctorVisit {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorAppointment->refresh()->load(['visit', 'queueEntry', 'doctorFacility.facility'])->loadSum('payments as paid_minor', 'amount_minor');
            if ($doctorAppointment->visit) {
                return $doctorAppointment->visit;
            }
            $this->activeContext($doctor, $doctorAppointment);
            if ($doctorAppointment->revision !== $request->integer('revision') || $doctorAppointment->queueState() !== 'called') {
                throw ValidationException::withMessages(['revision' => 'بدء الزيارة يتطلب موعدًا مؤكدًا اليوم، وحضورًا وتسديدًا كاملًا واستدعاءً مسجلًا. حدّث الموعد قبل المحاولة.']);
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
        $rules = ['revision' => ['required', 'integer', 'min:1'], 'intent' => ['required', Rule::in(['save', 'close'])], 'status' => ['required', Rule::in(['draft', 'initial', 'pending_results'])], 'diagnosis_type' => [$closing ? 'required_with:diagnosis' : 'nullable', Rule::in(['initial', 'confirmed'])]];
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
            $doctorVisit->update(['content' => $content, 'status' => $closing ? 'completed' : $data['status'], 'closed_at' => $closing ? now('UTC') : null, 'revision' => $doctorVisit->revision + 1]);
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
        $url = route('doctor.clinical.visit', ['visit' => $visit->id]);

        return $request->expectsJson() ? response()->json(compact('message', 'url')) : redirect($url)->with('status', $message);
    }
}
