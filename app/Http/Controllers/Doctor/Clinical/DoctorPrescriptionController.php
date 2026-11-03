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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DoctorPrescriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $data = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'status' => ['nullable', Rule::in(['draft', 'approved'])], 'sort' => ['nullable', Rule::in(['newest', 'oldest', 'recent'])]]);
        $doctor = $request->user('doctor');
        $base = DoctorPrescription::query()->whereBelongsTo($doctor);
        $counts = ['total' => (clone $base)->count(), 'draft' => (clone $base)->where('status', 'draft')->count(), 'approved' => (clone $base)->where('status', 'approved')->count()];
        $prescriptions = $base->with(['doctorVisit.doctorAppointment.doctorFacility.facility', 'correctedFrom:id,issued_snapshot', 'correction:id,corrects_prescription_id,approved_at,status'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $data['status']))
            ->when(($data['sort'] ?? '') === 'recent', fn ($query) => $query->where('created_at', '>=', now()->subDays(30)))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('issued_snapshot->patient_name', 'like', '%'.$data['search'].'%')->orWhereHas('doctorVisit.doctorAppointment', fn ($query) => $query->where(fn ($query) => $query->where('patient_name', 'like', '%'.$data['search'].'%')->orWhere('patient_phone', 'like', '%'.$data['search'].'%')))))
            ->orderBy('id', ($data['sort'] ?? '') === 'oldest' ? 'asc' : 'desc')->paginate(15)->withQueryString();

        return response()->view('doctor.clinical.prescriptions-workspace', compact('doctor', 'prescriptions', 'counts'))->header('Cache-Control', 'private, no-store');
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
        $request->validate(['inline' => ['sometimes', 'boolean'], 'inline_visit' => ['required_if:inline,1', 'nullable', 'integer', 'min:1']]);
        $inline = $request->boolean('inline');
        abort_if($inline && $doctorPrescription->doctor_visit_id !== $request->integer('inline_visit'), 404);
        $doctor = $request->user('doctor');
        $doctorPrescription->load(['correctedFrom', 'correction', 'doctorVisit.doctorAppointment.doctorFacility.facility', 'revisions' => fn ($query) => $query->where('action', '!=', 'viewed')->select(['id', 'doctor_prescription_id', 'action', 'revision', 'created_at'])]);
        $doctorPrescription->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'viewed', 'revision' => $doctorPrescription->revision]);
        $medications = Medication::query()->where('status', 'approved')->orderBy('name')->get();
        $displayItems = collect($doctorPrescription->content['items'] ?? [])->map(function (array $item) use ($medications): array {
            if (($item['source'] ?? null) === 'custom' || ! $medications->contains('id', $item['medication_id'] ?? null)) {
                return array_replace($item, ['source' => 'custom', 'medication_id' => null]);
            }

            return $item + ['source' => 'catalog'];
        })->all();

        return response()->view($inline ? 'doctor.clinical.document-inline' : 'doctor.clinical.prescription-workspace', ['doctor' => $doctor, 'prescription' => $doctorPrescription, 'visit' => $doctorPrescription->doctorVisit, 'appointment' => $doctorPrescription->doctorVisit->doctorAppointment, 'medications' => $medications, 'displayItems' => $displayItems, 'inline' => $inline, 'inlineKind' => 'prescription'])->header('Cache-Control', 'private, no-store');
    }

    public function update(SaveDoctorPrescriptionRequest $request, DoctorPrescription $doctorPrescription): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $approving = $data['intent'] === 'approve';
        DB::transaction(function () use ($request, $doctorPrescription, $data, $approving): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorPrescription->refresh()->load('doctorVisit');
            $visit = $doctorPrescription->doctorVisit;
            $this->writableContext($doctor, $visit, (bool) $doctorPrescription->corrects_prescription_id);
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
                'visit_date' => $visit->started_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i'),
                'specialty' => $doctor->primarySpecialtyAssignment()->where('verification_status', 'verified')->with('specialty')->first()?->specialty?->name_ar,
                'stamp_data_uri' => $this->stampDataUri($doctor),
                'logo_data_uri' => 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/doctor/thera-logo.png'))),
            ] : null;
            if ($approving && $doctorPrescription->corrects_prescription_id) {
                $previous = $doctorPrescription->correctedFrom;
                foreach (['patient_name', 'patient_phone', 'facility_name', 'visit_date', 'visit_id', 'appointment_id'] as $field) {
                    $snapshot[$field] = $previous->issued_snapshot[$field] ?? null;
                }
            }
            $doctorPrescription->update(['content' => $content, 'status' => $approving ? 'approved' : 'draft', 'approved_at' => $approving ? now('UTC') : null, 'issued_snapshot' => $snapshot, 'revision' => $doctorPrescription->revision + 1]);
            $doctorPrescription->revisions()->create(['doctor_id' => $doctor->id, 'action' => $approving ? 'approved' : 'saved', 'revision' => $doctorPrescription->revision, 'snapshot' => ['content' => $content, 'status' => $doctorPrescription->status, 'issued_snapshot' => $snapshot]]);
        });

        return $this->saved($request, $doctorPrescription, $approving ? 'اعتُمدت الوصفة وحُفظ إصدارها. لم تُرسل لصيدلية ولم يُسجل صرف دواء.' : 'حُفظت مسودة الوصفة على الخادم.');
    }

    public function correct(Request $request, DoctorPrescription $doctorPrescription): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorPrescription);
        $data = $request->validate(['reason' => ['required', 'string', 'min:10', 'max:1000'], 'revision' => ['required', 'integer', 'min:1']], [
            'reason.required' => 'اكتب سبب إنشاء إصدار تصحيحي.', 'reason.min' => 'وضح سبب التصحيح بعشرة أحرف على الأقل.', 'reason.max' => 'سبب التصحيح لا يتجاوز 1000 حرف.',
        ]);
        if (! trim($data['reason'])) {
            throw ValidationException::withMessages(['reason' => 'اكتب سبب إنشاء إصدار تصحيحي.']);
        }
        $correction = DB::transaction(function () use ($request, $doctorPrescription, $data): DoctorPrescription {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorPrescription->refresh();
            abort_unless($doctorPrescription->approved_at && $doctorPrescription->status === 'approved' && is_array($doctorPrescription->issued_snapshot), 409, 'التصحيح يبدأ من وصفة معتمدة فقط.');
            if ($doctorPrescription->revision !== (int) $data['revision']) {
                throw ValidationException::withMessages(['revision' => 'تغير الإصدار. أعد فتح الوصفة قبل التصحيح.']);
            }
            $this->writableContext($doctor, $doctorPrescription->doctorVisit, true);
            if ($existing = $doctorPrescription->correction()->first()) {
                return $existing;
            }
            $correction = DoctorPrescription::query()->create([
                'doctor_visit_id' => $doctorPrescription->doctor_visit_id, 'doctor_id' => $doctor->id,
                'status' => 'draft', 'content' => $doctorPrescription->content, 'revision' => 1,
                'corrects_prescription_id' => $doctorPrescription->id, 'document_version' => $doctorPrescription->document_version + 1,
                'correction_reason' => trim($data['reason']),
            ]);
            $correction->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'correction_created', 'revision' => 1, 'snapshot' => [
                'source_id' => $doctorPrescription->id, 'source_revision' => $doctorPrescription->revision,
                'reason' => $correction->correction_reason, 'content' => $correction->content, 'status' => 'draft',
            ]]);

            return $correction;
        });

        return $this->saved($request, $correction, 'فُتح الإصدار التصحيحي المرتبط بالأصل. النسخة السابقة لا تستبدل إلا بعد اعتماد التصحيح.');
    }

    private function writableContext(Doctor $doctor, DoctorVisit $visit, bool $correction = false): void
    {
        $visit->loadMissing('doctorAppointment.doctorFacility.facility');
        $appointment = $visit->doctorAppointment;
        if ((! $correction && $visit->closed_at) || $doctor->verification_status !== 'verified' || ! $appointment->arrived_at || ! in_array($appointment->status, $correction ? ['confirmed', 'completed'] : ['confirmed'], true) || $appointment->doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $appointment->doctorFacility->facility->is_active) {
            throw ValidationException::withMessages(['revision' => $correction ? 'التصحيح يتطلب طبيبًا موثقًا وعضوية ومنشأة نشطتين، وحضورًا مسجلًا وموعدًا مؤكدًا أو مكتملًا.' : 'حفظ الوصفة واعتمادها يتطلبان زيارة مفتوحة وطبيبًا موثقًا وعضوية ومنشأة نشطتين.']);
        }
    }

    private function stampDataUri(Doctor $doctor): ?string
    {
        if (! $doctor->stamp_path) {
            return null;
        }
        if (! Storage::disk('local')->exists($doctor->stamp_path)) {
            throw ValidationException::withMessages(['stamp' => 'صورة الختم المحفوظة غير متاحة. أعد رفعها قبل الاعتماد.']);
        }
        $bytes = Storage::disk('local')->get($doctor->stamp_path);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
        if (! in_array($mime, ['image/png', 'image/jpeg'], true) || strlen($bytes) > 1048576) {
            throw ValidationException::withMessages(['stamp' => 'صورة الختم غير صالحة. أعد رفعها قبل الاعتماد.']);
        }

        return 'data:'.$mime.';base64,'.base64_encode($bytes);
    }

    private function saved(Request $request, DoctorPrescription $prescription, string $message): JsonResponse|RedirectResponse
    {
        $url = route('doctor.clinical.prescriptions.show', $prescription);

        return $request->expectsJson() ? response()->json(compact('url', 'message')) : redirect($url)->with('status', $message);
    }
}
