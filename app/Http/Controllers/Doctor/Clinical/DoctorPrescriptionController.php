<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Clinical\SaveDoctorPrescriptionRequest;
use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\DoctorPrescription;
use App\Models\DoctorVisit;
use App\Models\Medication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class DoctorPrescriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $data = $request->validate(['search' => ['nullable', 'string', 'max:100']]);
        $doctor = $request->user('doctor');
        $prescriptions = DoctorPrescription::query()->whereBelongsTo($doctor)->with('doctorVisit.doctorAppointment.doctorFacility.facility')
            ->when($request->filled('search'), fn ($query) => $query->whereHas('doctorVisit.doctorAppointment', fn ($query) => $query->where('patient_name', 'like', '%'.$data['search'].'%')))
            ->latest()->paginate(15)->withQueryString();

        return response()->view('doctor.clinical.prescriptions-workspace', compact('doctor', 'prescriptions'))->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request, DoctorVisit $doctorVisit): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorVisit);
        $prescription = DB::transaction(function () use ($request, $doctorVisit): DoctorPrescription {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $existing = $doctorVisit->prescription()->first();
            if ($existing) {
                return $existing;
            }
            $this->writableContext($doctor, $doctorVisit->fresh());
            $prescription = $doctorVisit->prescription()->create(['doctor_id' => $doctor->id, 'status' => 'draft', 'content' => ['items' => [], 'notes' => null], 'revision' => 1]);
            $prescription->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'created', 'revision' => 1, 'snapshot' => ['content' => $prescription->content, 'status' => 'draft']]);

            return $prescription;
        });

        return $this->saved($request, $prescription, 'فُتحت مسودة الوصفة المرتبطة بالزيارة. لم تُعتمد أو تُرسل للصرف.');
    }

    public function show(Request $request, DoctorPrescription $doctorPrescription): Response
    {
        Gate::forUser($request->user('doctor'))->authorize('view', $doctorPrescription);
        $doctor = $request->user('doctor');
        $doctorPrescription->load(['doctorVisit.doctorAppointment.doctorFacility.facility', 'revisions' => fn ($query) => $query->where('action', '!=', 'viewed')->select(['id', 'doctor_prescription_id', 'action', 'revision', 'created_at'])]);
        $doctorPrescription->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'viewed', 'revision' => $doctorPrescription->revision]);
        $medications = Medication::query()->where('status', 'approved')->orderBy('name')->get();
        $displayItems = collect($doctorPrescription->content['items'] ?? [])->map(function (array $item) use ($medications): array {
            if (($item['source'] ?? null) === 'custom' || ! $medications->contains('id', $item['medication_id'] ?? null)) {
                return array_replace($item, ['source' => 'custom', 'medication_id' => null]);
            }

            return $item + ['source' => 'catalog'];
        })->all();

        return response()->view('doctor.clinical.prescription-workspace', ['doctor' => $doctor, 'prescription' => $doctorPrescription, 'visit' => $doctorPrescription->doctorVisit, 'appointment' => $doctorPrescription->doctorVisit->doctorAppointment, 'medications' => $medications, 'displayItems' => $displayItems])->header('Cache-Control', 'private, no-store');
    }

    public function update(SaveDoctorPrescriptionRequest $request, DoctorPrescription $doctorPrescription): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $approving = $data['intent'] === 'approve';
        DB::transaction(function () use ($request, $doctorPrescription, $data, $approving): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorPrescription->refresh()->load('doctorVisit');
            $visit = $doctorPrescription->doctorVisit;
            $this->writableContext($doctor, $visit);
            if ($doctorPrescription->approved_at || $doctorPrescription->revision !== (int) $data['revision']) {
                throw ValidationException::withMessages(['revision' => 'الوصفة معتمدة أو تغير إصدارها. أعد فتحها قبل المتابعة.']);
            }
            $medications = Medication::query()->whereIn('id', collect($data['items'])->pluck('medication_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $items = [];
            foreach ($data['items'] as $index => $item) {
                if ($item['source'] === 'custom') {
                    $items[] = $item + ['medication_id' => null];

                    continue;
                }
                $medication = $medications->get($item['medication_id']);
                if (! $medication || ($medication->status !== 'approved' && $medication->doctor_id !== $doctor->id)) {
                    throw ValidationException::withMessages(['items.'.$index.'.medication_id' => 'الدواء المحدد غير متاح لهذه الوصفة.']);
                }
                if ($approving && $medication->status !== 'approved') {
                    throw ValidationException::withMessages(['items.'.$index.'.medication_id' => 'هذا الدواء غير متاح في الدليل. اكتبه كدواء خاص بهذه الوصفة بدل اختياره من الدليل.']);
                }
                $items[] = collect($item)->only(['source', 'medication_id', 'dose', 'frequency', 'duration', 'quantity', 'instructions'])->all() + $medication->only(['name', 'active_ingredient', 'strength', 'dosage_form']);
            }
            $content = ['items' => $items, 'notes' => $data['notes'] ?? null];
            $snapshot = $approving ? [
                'doctor_name' => $doctor->name, 'professional_title' => $doctor->professional_title,
                'patient_name' => $visit->doctorAppointment->patient_name, 'patient_phone' => $visit->doctorAppointment->patient_phone,
                'facility_name' => $visit->doctorAppointment->doctorFacility->facility->name,
                'visit_id' => $visit->id, 'appointment_id' => $visit->doctor_appointment_id,
            ] : null;
            $doctorPrescription->update(['content' => $content, 'status' => $approving ? 'approved' : 'draft', 'approved_at' => $approving ? now('UTC') : null, 'issued_snapshot' => $snapshot, 'revision' => $doctorPrescription->revision + 1]);
            $doctorPrescription->revisions()->create(['doctor_id' => $doctor->id, 'action' => $approving ? 'approved' : 'saved', 'revision' => $doctorPrescription->revision, 'snapshot' => ['content' => $content, 'status' => $doctorPrescription->status, 'issued_snapshot' => $snapshot]]);
        });

        return $this->saved($request, $doctorPrescription, $approving ? 'اعتُمدت الوصفة وحُفظ إصدارها. لم تُرسل لصيدلية ولم يُسجل صرف دواء.' : 'حُفظت مسودة الوصفة على الخادم.');
    }

    private function writableContext(Doctor $doctor, DoctorVisit $visit): void
    {
        $visit->loadMissing('doctorAppointment.doctorFacility.facility');
        $appointment = $visit->doctorAppointment;
        if ($visit->closed_at || $doctor->verification_status !== 'verified' || ! $appointment->arrived_at || $appointment->status !== 'confirmed' || $appointment->doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $appointment->doctorFacility->facility->is_active) {
            throw ValidationException::withMessages(['revision' => 'حفظ الوصفة واعتمادها يتطلبان زيارة مفتوحة وطبيبًا موثقًا وعضوية ومنشأة نشطتين.']);
        }
    }

    private function saved(Request $request, DoctorPrescription $prescription, string $message): JsonResponse|RedirectResponse
    {
        $url = route('doctor.clinical.prescriptions.show', $prescription);

        return $request->expectsJson() ? response()->json(compact('url', 'message')) : redirect($url)->with('status', $message);
    }
}
