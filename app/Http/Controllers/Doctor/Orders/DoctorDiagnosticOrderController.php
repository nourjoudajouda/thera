<?php

namespace App\Http\Controllers\Doctor\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Orders\SaveDoctorDiagnosticOrderRequest;
use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorFacility;
use App\Models\DoctorVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DoctorDiagnosticOrderController extends Controller
{
    public function lab(Request $request): Response
    {
        return $this->index($request, 'lab');
    }

    public function radiology(Request $request): Response
    {
        return $this->index($request, 'radiology');
    }

    private function index(Request $request, string $kind): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(DoctorDiagnosticOrder::STATUSES))],
            'priority' => ['nullable', Rule::in(array_keys(DoctorDiagnosticOrder::PRIORITIES))],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'urgent'])],
            'result' => ['nullable', Rule::in(['none', 'external', 'reviewed'])],
        ]);
        $doctor = $request->user('doctor');
        $base = DoctorDiagnosticOrder::query()->whereBelongsTo($doctor)->where('kind', $kind);
        $counts = ['total' => (clone $base)->count(), 'draft' => (clone $base)->where('status', 'draft')->count(), 'approved' => (clone $base)->where('status', 'approved')->count()];
        $query = $base->with(['doctorVisit.doctorAppointment.doctorFacility.facility', 'correction:id,corrects_document_id,approved_at'])->withCount(['externalReports', 'externalReports as reviewed_reports_count' => fn ($query) => $query->whereNotNull('reviewed_at')])
            ->when(($filters['result'] ?? '') === 'none', fn ($query) => $query->doesntHave('externalReports'))
            ->when(($filters['result'] ?? '') === 'external', fn ($query) => $query->has('externalReports'))
            ->when(($filters['result'] ?? '') === 'reviewed', fn ($query) => $query->whereHas('externalReports', fn ($query) => $query->whereNotNull('reviewed_at')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $filters['status']))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $filters['priority']))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query
                ->where('issued_snapshot->patient_name', 'like', '%'.$filters['search'].'%')
                ->orWhere('issued_snapshot->patient_phone', 'like', '%'.$filters['search'].'%')
                ->orWhereHas('doctorVisit.doctorAppointment', fn ($query) => $query->where(fn ($query) => $query->where('patient_name', 'like', '%'.$filters['search'].'%')->orWhere('patient_phone', 'like', '%'.$filters['search'].'%')))));
        if (($filters['sort'] ?? '') === 'urgent') {
            $query->orderByDesc('priority');
        }
        $orders = $query->orderBy('id', ($filters['sort'] ?? '') === 'oldest' ? 'asc' : 'desc')->paginate(15)->withQueryString();

        return response()->view('doctor.orders.diagnostic-index', compact('doctor', 'kind', 'counts', 'orders'))->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request, DoctorVisit $doctorVisit, string $kind): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorVisit);
        abort_unless(array_key_exists($kind, DoctorDiagnosticOrder::KINDS), 404);
        $data = $request->validate(['request_key' => ['required', 'uuid']], ['request_key.required' => 'أعد فتح الزيارة قبل إنشاء الطلب.', 'request_key.uuid' => 'أعد فتح الزيارة قبل إنشاء الطلب.']);
        $order = DB::transaction(function () use ($request, $doctorVisit, $kind, $data): DoctorDiagnosticOrder {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $existing = DoctorDiagnosticOrder::query()->whereBelongsTo($doctor)->where('request_key', $data['request_key'])->first();
            if ($existing) {
                if ($existing->doctor_visit_id !== $doctorVisit->id || $existing->kind !== $kind) {
                    throw ValidationException::withMessages(['request_key' => 'مفتاح العملية مرتبط بطلب مختلف. أعد فتح الزيارة.']);
                }

                return $existing;
            }
            $this->writableContext($doctor, $doctorVisit->fresh());
            $order = $doctorVisit->diagnosticOrders()->create(['doctor_id' => $doctor->id, 'kind' => $kind, 'request_key' => $data['request_key'], 'status' => 'draft', 'priority' => 'normal', 'content' => ['items' => []], 'revision' => 1]);
            $order->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'created', 'revision' => 1, 'snapshot' => ['content' => $order->content, 'status' => 'draft']]);

            return $order;
        }, 3);

        return $this->saved($request, $order, 'فُتحت مسودة طلب مرتبطة بالزيارة. لم يُعتمد الطلب أو يُرسل لجهة تنفيذ.');
    }

    public function show(Request $request, DoctorDiagnosticOrder $doctorDiagnosticOrder): Response
    {
        Gate::forUser($request->user('doctor'))->authorize('view', $doctorDiagnosticOrder);
        $request->validate(['inline' => ['sometimes', 'boolean'], 'inline_visit' => ['required_if:inline,1', 'nullable', 'integer', 'min:1']]);
        $inline = $request->boolean('inline');
        abort_if($inline && $doctorDiagnosticOrder->doctor_visit_id !== $request->integer('inline_visit'), 404);
        $doctor = $request->user('doctor');
        $doctorDiagnosticOrder->load(['doctorVisit.doctorAppointment.doctorFacility.facility', 'correctedFrom', 'correction', 'revisions' => fn ($query) => $query->where('action', '!=', 'viewed')->select(['id', 'doctor_diagnostic_order_id', 'action', 'revision', 'created_at'])]);
        $doctorDiagnosticOrder->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'viewed', 'revision' => $doctorDiagnosticOrder->revision]);

        return response()->view($inline ? 'doctor.clinical.document-inline' : 'doctor.orders.diagnostic-workspace', ['doctor' => $doctor, 'order' => $doctorDiagnosticOrder, 'visit' => $doctorDiagnosticOrder->doctorVisit, 'appointment' => $doctorDiagnosticOrder->doctorVisit->doctorAppointment, 'inline' => $inline, 'inlineKind' => $doctorDiagnosticOrder->kind])->header('Cache-Control', 'private, no-store');
    }

    public function update(SaveDoctorDiagnosticOrderRequest $request, DoctorDiagnosticOrder $doctorDiagnosticOrder): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $approving = $data['intent'] === 'approve';
        DB::transaction(function () use ($request, $doctorDiagnosticOrder, $data, $approving): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorDiagnosticOrder->refresh()->load('doctorVisit');
            $this->writableContext($doctor, $doctorDiagnosticOrder->doctorVisit, (bool) $doctorDiagnosticOrder->corrects_document_id);
            if ($doctorDiagnosticOrder->approved_at || $doctorDiagnosticOrder->revision !== (int) $data['revision']) {
                throw ValidationException::withMessages(['revision' => 'الطلب معتمد أو تغيرت نسخته. أعد فتحه قبل المتابعة.']);
            }
            $content = collect($data)->only(['clinical_context', 'clinical_question', 'instructions', 'items'])->all();
            $content['items'] = array_values($content['items']);
            if ($approving) {
                $content['items'] = array_map(fn (array $item): array => $item + ['id' => (string) Str::uuid(), 'source' => 'custom'], $content['items']);
            }
            $snapshot = $approving ? $this->issuedSnapshot($doctor, $doctorDiagnosticOrder->doctorVisit) : null;
            if ($approving && $doctorDiagnosticOrder->corrects_document_id) {
                foreach (['patient_name', 'patient_phone', 'facility_name', 'visit_id', 'appointment_id', 'visit_date'] as $field) {
                    $snapshot[$field] = $doctorDiagnosticOrder->correctedFrom->issued_snapshot[$field] ?? null;
                }
            }
            $doctorDiagnosticOrder->update(['priority' => $data['priority'], 'content' => $content, 'issued_snapshot' => $snapshot, 'status' => $approving ? 'approved' : 'draft', 'approved_at' => $approving ? now('UTC') : null, 'revision' => $doctorDiagnosticOrder->revision + 1]);
            $doctorDiagnosticOrder->revisions()->create(['doctor_id' => $doctor->id, 'action' => $approving ? 'approved' : 'saved', 'revision' => $doctorDiagnosticOrder->revision, 'snapshot' => ['content' => $content, 'priority' => $data['priority'], 'status' => $doctorDiagnosticOrder->status, 'issued_snapshot' => $snapshot]]);
            $doctorDiagnosticOrder->doctorVisit->synchronizeWorkflowStatus();
        }, 3);

        return $this->saved($request, $doctorDiagnosticOrder, $approving ? 'اعتُمد طلب الفحوصات وحُفظ إصداره. لم يُرسل إلى مختبر أو مركز أشعة ولم يُحجز موعد أو يُحصّل مبلغ.' : 'حُفظت مسودة الطلب على الخادم.');
    }

    private function writableContext(Doctor $doctor, DoctorVisit $visit, bool $correction = false): void
    {
        $visit->loadMissing('doctorAppointment.doctorFacility.facility');
        $appointment = $visit->doctorAppointment;
        if (($visit->closed_at && ! $correction) || $doctor->verification_status !== 'verified' || $appointment->doctor_id !== $doctor->id || $appointment->doctor_facility_id !== $visit->doctor_facility_id || ! $appointment->arrived_at || ! in_array($appointment->status, $correction ? ['confirmed', 'completed'] : ['confirmed'], true) || $appointment->doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $appointment->doctorFacility->facility->is_active) {
            throw ValidationException::withMessages(['revision' => 'إنشاء الطلب وحفظه واعتماده يتطلب زيارة مفتوحة وحضورًا مسجلًا وطبيبًا موثقًا وعضوية ومنشأة نشطتين.']);
        }
    }

    /** @return array<string, mixed> */
    private function issuedSnapshot(Doctor $doctor, DoctorVisit $visit): array
    {
        $stamp = null;
        if ($doctor->stamp_path) {
            if (! Storage::disk('local')->exists($doctor->stamp_path)) {
                throw ValidationException::withMessages(['stamp' => 'صورة الختم غير متاحة. أعد رفعها قبل اعتماد الطلب.']);
            }
            $bytes = Storage::disk('local')->get($doctor->stamp_path);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
            if (! in_array($mime, ['image/png', 'image/jpeg'], true) || strlen($bytes) > 1048576) {
                throw ValidationException::withMessages(['stamp' => 'صورة الختم غير صالحة. أعد رفعها قبل اعتماد الطلب.']);
            }
            $stamp = 'data:'.$mime.';base64,'.base64_encode($bytes);
        }

        return [
            'patient_name' => $visit->doctorAppointment->patient_name, 'patient_phone' => $visit->doctorAppointment->patient_phone,
            'doctor_name' => $doctor->name, 'professional_title' => $doctor->professional_title,
            'specialty' => $doctor->primarySpecialtyAssignment()->where('verification_status', 'verified')->with('specialty')->first()?->specialty?->name_ar,
            'facility_name' => $visit->doctorAppointment->doctorFacility->facility->name,
            'visit_id' => $visit->id, 'appointment_id' => $visit->doctor_appointment_id,
            'visit_date' => $visit->started_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i'),
            'stamp_data_uri' => $stamp, 'logo_data_uri' => 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/doctor/thera-logo.png'))),
        ];
    }

    private function saved(Request $request, DoctorDiagnosticOrder $order, string $message): JsonResponse|RedirectResponse
    {
        $url = route('doctor.orders.show', $order);

        return $request->expectsJson() ? response()->json(compact('url', 'message')) : redirect($url)->with('status', $message);
    }
}
