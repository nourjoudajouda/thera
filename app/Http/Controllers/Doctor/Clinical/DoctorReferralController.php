<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Clinical\SaveDoctorReferralRequest;
use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DoctorReferralController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'kind' => ['nullable', Rule::in(array_keys(DoctorReferral::KINDS))], 'status' => ['nullable', Rule::in(array_keys(DoctorReferral::STATUSES))], 'priority' => ['nullable', Rule::in(array_keys(DoctorReferral::PRIORITIES))], 'sort' => ['nullable', Rule::in(['newest', 'oldest', 'urgent'])]]);
        $doctor = $request->user('doctor');
        $base = DoctorReferral::query()->whereBelongsTo($doctor);
        $counts = ['total' => (clone $base)->count(), 'draft' => (clone $base)->where('status', 'draft')->count(), 'approved' => (clone $base)->where('status', 'approved')->count()];
        $kindCounts = ['referral' => (clone $base)->where('kind', 'referral')->count(), 'followup' => (clone $base)->where('kind', 'followup')->count()];
        foreach (['kind', 'status', 'priority'] as $field) {
            if ($request->filled($field)) {
                $base->where($field, $filters[$field]);
            }
        }
        $documents = $base->with(['doctorVisit.doctorAppointment', 'targetDoctor:id,name', 'targetFacility:id,name', 'correction:id,corrects_document_id,approved_at'])
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('content->title', 'like', '%'.$filters['search'].'%')->orWhere('issued_snapshot->patient_name', 'like', '%'.$filters['search'].'%')->orWhereHas('doctorVisit.doctorAppointment', fn ($query) => $query->where('patient_name', 'like', '%'.$filters['search'].'%'))))
            ->when(($filters['sort'] ?? '') === 'urgent', fn ($query) => $query->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 ELSE 1 END"))
            ->orderBy('id', ($filters['sort'] ?? '') === 'oldest' ? 'asc' : 'desc')->paginate(15)->withQueryString();

        return response()->view('doctor.clinical.referrals-index', compact('doctor', 'counts', 'kindCounts', 'documents'))->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request, DoctorVisit $doctorVisit, string $kind): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorVisit);
        abort_unless(array_key_exists($kind, DoctorReferral::KINDS), 404);
        $data = $request->validate(['request_key' => ['required', 'uuid']]);
        $referral = DB::transaction(function () use ($request, $doctorVisit, $kind, $data): DoctorReferral {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $existing = DoctorReferral::query()->whereBelongsTo($doctor)->where('request_key', $data['request_key'])->first();
            if ($existing) {
                if ($existing->doctor_visit_id !== $doctorVisit->id || $existing->kind !== $kind) {
                    throw ValidationException::withMessages(['request_key' => 'العملية مرتبطة بمستند آخر؛ أعد فتح الزيارة.']);
                }

                return $existing;
            }
            $this->writableContext($doctor, $doctorVisit->fresh());
            $document = $doctorVisit->referrals()->create(['doctor_id' => $doctor->id, 'kind' => $kind, 'status' => 'draft', 'priority' => 'normal', 'content' => [], 'revision' => 1, 'request_key' => $data['request_key']]);
            $this->record($document, 'created');

            return $document;
        }, 3);

        return $this->saved($request, $referral, 'فُتحت مسودة مرتبطة بالزيارة. لم يُنشأ موعد أو يُرسل مستند.');
    }

    public function show(Request $request, DoctorReferral $doctorReferral): Response
    {
        Gate::forUser($request->user('doctor'))->authorize('view', $doctorReferral);
        $request->validate(['inline' => ['sometimes', 'boolean'], 'inline_visit' => ['required_if:inline,1', 'nullable', 'integer', 'min:1']]);
        $inline = $request->boolean('inline');
        abort_if($inline && $doctorReferral->doctor_visit_id !== $request->integer('inline_visit'), 404);
        $doctor = $request->user('doctor');
        $doctorReferral->load(['doctorVisit.doctorAppointment.doctorFacility.facility', 'targetDoctor:id,name', 'targetFacility:id,name', 'correctedFrom', 'correction', 'revisions' => fn ($query) => $query->where('action', '!=', 'viewed')->select(['id', 'doctor_referral_id', 'action', 'revision', 'created_at'])]);
        $doctorReferral->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'viewed', 'revision' => $doctorReferral->revision]);
        $readonly = (bool) $doctorReferral->approved_at || ((bool) $doctorReferral->doctorVisit->closed_at && ! $doctorReferral->corrects_document_id);
        $targets = [];
        if (! $readonly && $doctorReferral->kind === 'referral') {
            foreach ($this->eligibleDoctors($doctor)->orderBy('name')->get(['id', 'name', 'professional_title']) as $target) {
                $targets['doctor:'.$target->id] = 'طبيب: '.$target->name.' — '.$target->professional_title;
            }
            foreach (Facility::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'city']) as $target) {
                $targets['facility:'.$target->id] = 'منشأة: '.$target->name.' — '.$target->city;
            }
        }

        return response()->view($inline ? 'doctor.clinical.document-inline' : 'doctor.clinical.referral-workspace', ['doctor' => $doctor, 'document' => $doctorReferral, 'visit' => $doctorReferral->doctorVisit, 'appointment' => $doctorReferral->doctorVisit->doctorAppointment, 'readonly' => $readonly, 'targets' => $targets, 'inline' => $inline, 'inlineKind' => $doctorReferral->kind])->header('Cache-Control', 'private, no-store');
    }

    public function update(SaveDoctorReferralRequest $request, DoctorReferral $doctorReferral): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $doctorReferral, $data): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorReferral->refresh()->load('doctorVisit');
            $visit = $doctorReferral->doctorVisit;
            $this->writableContext($doctor, $visit, (bool) $doctorReferral->corrects_document_id);
            if ($doctorReferral->approved_at || $doctorReferral->revision !== (int) $data['revision']) {
                throw ValidationException::withMessages(['revision' => 'المستند معتمد أو تغير إصداره. أعد فتحه قبل المتابعة.']);
            }
            $targetDoctor = null;
            $targetFacility = null;
            if ($doctorReferral->kind === 'referral' && ! empty($data['target'])) {
                [$type, $id] = explode(':', $data['target']);
                $targetDoctor = $type === 'doctor' ? $this->eligibleDoctors($doctor)->whereKey($id)->lockForUpdate()->first() : null;
                $targetFacility = $type === 'facility' ? Facility::query()->where('is_active', true)->whereKey($id)->lockForUpdate()->first() : null;
                if (! $targetDoctor && ! $targetFacility) {
                    throw ValidationException::withMessages(['target' => 'الجهة غير متاحة؛ اختر طبيبًا موثقًا مرتبطًا بمنشأة نشطة، أو منشأة نشطة مسجلة بالنظام.']);
                }
            }
            $content = collect($data)->only(['title', 'reason', 'clinical_summary', 'instructions', 'due_date', 'followup_kind', 'time_window'])->all();
            $approving = $data['intent'] === 'approve';
            $snapshot = $approving ? $this->snapshot($doctor, $visit) + ['target_name' => $targetDoctor?->name ?? $targetFacility?->name, 'target_type' => $targetDoctor ? 'doctor' : ($targetFacility ? 'facility' : null)] : null;
            if ($approving && $doctorReferral->corrects_document_id) {
                foreach (['patient_name', 'patient_phone', 'facility_name', 'visit_id', 'appointment_id', 'visit_date'] as $field) {
                    $snapshot[$field] = $doctorReferral->correctedFrom->issued_snapshot[$field] ?? null;
                }
            }
            $doctorReferral->update(['content' => $content, 'priority' => $data['priority'], 'target_doctor_id' => $targetDoctor?->id, 'target_facility_id' => $targetFacility?->id, 'status' => $approving ? 'approved' : 'draft', 'approved_at' => $approving ? now('UTC') : null, 'issued_snapshot' => $snapshot, 'revision' => $doctorReferral->revision + 1]);
            $this->record($doctorReferral, $approving ? 'approved' : 'saved');
        }, 3);

        return $this->saved($request, $doctorReferral, $data['intent'] === 'approve' ? 'اعتُمد المستند وحُفظ للقراءة فقط. لم يُرسل لجهة أخرى ولم يُنشأ موعد.' : 'حُفظت المسودة على الخادم.');
    }

    private function eligibleDoctors(Doctor $doctor): Builder
    {
        return Doctor::query()->where('id', '!=', $doctor->id)->where('verification_status', 'verified')->whereHas('facilityMemberships', fn ($query) => $query->where('membership_status', DoctorFacility::STATUS_ACTIVE)->whereHas('facility', fn ($query) => $query->where('is_active', true)));
    }

    private function writableContext(Doctor $doctor, DoctorVisit $visit, bool $correction = false): void
    {
        $visit->loadMissing('doctorAppointment.doctorFacility.facility');
        $appointment = $visit->doctorAppointment;
        if (($visit->closed_at && ! $correction) || $doctor->verification_status !== 'verified' || $appointment->doctor_id !== $doctor->id || $appointment->doctor_facility_id !== $visit->doctor_facility_id || ! $appointment->arrived_at || ! in_array($appointment->status, $correction ? ['confirmed', 'completed'] : ['confirmed'], true) || $appointment->doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $appointment->doctorFacility->facility->is_active) {
            throw ValidationException::withMessages(['revision' => 'يلزم طبيب موثق وزيارة مفتوحة بحضور مسجل وموعد مؤكد وعضوية ومنشأة نشطتين.']);
        }
    }

    private function record(DoctorReferral $document, string $action): void
    {
        $document->revisions()->create(['doctor_id' => $document->doctor_id, 'action' => $action, 'revision' => $document->revision, 'snapshot' => $document->only(['content', 'priority', 'status', 'issued_snapshot', 'target_doctor_id', 'target_facility_id'])]);
    }

    private function snapshot(Doctor $doctor, DoctorVisit $visit): array
    {
        $stamp = null;
        if ($doctor->stamp_path) {
            if (! Storage::disk('local')->exists($doctor->stamp_path)) {
                throw ValidationException::withMessages(['stamp' => 'الختم المحفوظ غير متاح. أعد رفعه قبل الاعتماد.']);
            }
            $bytes = Storage::disk('local')->get($doctor->stamp_path);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
            if (! in_array($mime, ['image/png', 'image/jpeg'], true) || strlen($bytes) > 1048576) {
                throw ValidationException::withMessages(['stamp' => 'الختم المحفوظ غير صالح.']);
            }
            $stamp = 'data:'.$mime.';base64,'.base64_encode($bytes);
        }

        return ['patient_name' => $visit->doctorAppointment->patient_name, 'patient_phone' => $visit->doctorAppointment->patient_phone, 'doctor_name' => $doctor->name, 'professional_title' => $doctor->professional_title,
            'facility_name' => $visit->doctorAppointment->doctorFacility->facility->name, 'visit_id' => $visit->id, 'appointment_id' => $visit->doctor_appointment_id,
            'visit_date' => $visit->started_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i'), 'stamp_data_uri' => $stamp,
            'logo_data_uri' => 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/doctor/thera-logo.png')))];
    }

    private function saved(Request $request, DoctorReferral $document, string $message): JsonResponse|RedirectResponse
    {
        $url = route('doctor.clinical.referrals.show', $document);

        return $request->expectsJson() ? response()->json(compact('url', 'message')) : redirect($url)->with('status', $message);
    }
}
