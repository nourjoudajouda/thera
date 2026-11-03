<?php

namespace App\Http\Controllers\Doctor\Orders;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DoctorExternalReportController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'kind' => ['nullable', Rule::in(['lab', 'radiology'])], 'review' => ['nullable', Rule::in(['pending', 'reviewed'])], 'order' => ['nullable', 'integer', 'min:1'], 'sort' => ['nullable', Rule::in(['newest', 'oldest', 'pending'])]]);
        $doctor = $request->user('doctor');
        $order = null;
        if ($request->filled('order')) {
            $order = DoctorDiagnosticOrder::findOrFail($filters['order']);
            $this->authorizeOrder($request, $order);
            $order->load('doctorVisit.doctorAppointment', 'correction');
        }
        $base = DoctorExternalReport::where('doctor_id', $doctor->id);
        $counts = ['total' => (clone $base)->count(), 'pending' => (clone $base)->whereNull('reviewed_at')->count(), 'reviewed' => (clone $base)->whereNotNull('reviewed_at')->count()];
        $reports = $base->with('order.doctorVisit.doctorAppointment')
            ->when($order, fn ($query) => $query->where('doctor_diagnostic_order_id', $order->id))
            ->when($request->filled('kind'), fn ($query) => $query->whereHas('order', fn ($query) => $query->where('kind', $filters['kind'])))
            ->when(($filters['review'] ?? '') === 'pending', fn ($query) => $query->whereNull('reviewed_at'))
            ->when(($filters['review'] ?? '') === 'reviewed', fn ($query) => $query->whereNotNull('reviewed_at'))
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query->where('title', 'like', '%'.$filters['search'].'%')->orWhere('source_name', 'like', '%'.$filters['search'].'%')->orWhereHas('order', fn ($query) => $query->where('issued_snapshot->patient_name', 'like', '%'.$filters['search'].'%'))))
            ->when(($filters['sort'] ?? '') === 'pending', fn ($query) => $query->orderByRaw('reviewed_at IS NOT NULL'))
            ->orderBy('id', ($filters['sort'] ?? '') === 'oldest' ? 'asc' : 'desc')->paginate(12)->withQueryString();

        return response()->view('doctor.orders.results', compact('doctor', 'reports', 'counts', 'order'))->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request, DoctorDiagnosticOrder $doctorDiagnosticOrder): JsonResponse|RedirectResponse
    {
        $this->authorizeOrder($request, $doctorDiagnosticOrder);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'], 'source_name' => ['required', 'string', 'max:180'],
            'source_reference' => ['nullable', 'string', 'max:120'],
            'report_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:'.now(config('thera.schedule_timezone'))->toDateString()],
            'report_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
            'request_key' => ['required', 'uuid'], 'identity_confirmed' => ['accepted'],
        ], ['report_file.max' => 'حجم التقرير لا يتجاوز 5 ميجابايت.', 'report_file.mimes' => 'اختر ملف PDF أو صورة JPG أو PNG صحيحة.', 'identity_confirmed.accepted' => 'أكد مطابقة التقرير للمريض والطلب قبل الرفع.'],
            ['title' => 'عنوان التقرير', 'source_name' => 'الجهة المكتوبة على التقرير', 'report_date' => 'تاريخ التقرير', 'report_file' => 'ملف التقرير']);
        $path = null;
        try {
            $report = DB::transaction(function () use ($request, $doctorDiagnosticOrder, $data, &$path): DoctorExternalReport {
                $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
                $order = $doctorDiagnosticOrder->fresh();
                $this->writable($doctor, $order);
                $existing = DoctorExternalReport::where('doctor_id', $doctor->id)->where('request_key', $data['request_key'])->first();
                if ($existing) {
                    if ($existing->doctor_diagnostic_order_id !== $order->id) {
                        throw ValidationException::withMessages(['request_key' => 'العملية مرتبطة بطلب آخر. أعد فتح الصفحة.']);
                    }

                    return $existing;
                }
                $file = $data['report_file'];
                $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file->getRealPath());
                if (! in_array($mime, ['application/pdf', 'image/jpeg', 'image/png'], true)) {
                    throw ValidationException::withMessages(['report_file' => 'اختر ملف PDF أو صورة JPG أو PNG صحيحة.']);
                }
                $path = $file->store('doctors/'.$doctor->id.'/external-reports', 'local');
                abort_unless(is_string($path), 503, 'تعذر حفظ التقرير.');
                $report = $order->externalReports()->create([
                    'doctor_id' => $doctor->id, 'title' => $data['title'], 'source_name' => $data['source_name'],
                    'source_reference' => $data['source_reference'] ?? null, 'report_date' => $data['report_date'],
                    'file_path' => $path, 'file_mime' => $mime, 'file_size' => $file->getSize(),
                    'file_sha256' => hash_file('sha256', $file->getRealPath()), 'request_key' => $data['request_key'],
                ]);
                $this->audit($report, 'external_uploaded');
                $order->doctorVisit->synchronizeWorkflowStatus();

                return $report;
            });
        } catch (Throwable $exception) {
            if (is_string($path)) {
                Storage::disk('local')->delete($path);
            }
            throw $exception;
        }

        return $this->saved($request, $report, 'حُفظ التقرير الخارجي بانتظار مراجعتك. لم يُعتمد من THERA ولم تتغير حالة تنفيذ الطلب.');
    }

    public function show(Request $request, DoctorExternalReport $doctorExternalReport): Response
    {
        $this->authorizeOrder($request, $doctorExternalReport->order);
        $doctorExternalReport->load('order.doctorVisit.doctorAppointment', 'order.correction');
        $this->audit($doctorExternalReport, 'external_viewed');

        return response()->view('doctor.orders.external-report', ['doctor' => $request->user('doctor'), 'report' => $doctorExternalReport, 'order' => $doctorExternalReport->order])->header('Cache-Control', 'private, no-store');
    }

    public function review(Request $request, DoctorExternalReport $doctorExternalReport): JsonResponse|RedirectResponse
    {
        $this->authorizeOrder($request, $doctorExternalReport->order);
        $data = $request->validate(['review_note' => ['required', 'string', 'min:3', 'max:5000'], 'review_confirmed' => ['accepted']], ['review_confirmed.accepted' => 'أكد أنك قرأت التقرير وطابقت هوية المريض.'], ['review_note' => 'ملاحظة مراجعة الطبيب']);
        DB::transaction(function () use ($request, $doctorExternalReport, $data): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $report = $doctorExternalReport->fresh();
            $this->writable($doctor, $report->order, false);
            if ($report->reviewed_at) {
                if ($report->review_note !== $data['review_note']) {
                    throw ValidationException::withMessages(['review_note' => 'المراجعة محفوظة مسبقًا ولا تستبدل. حدّث الصفحة.']);
                }

                return;
            }
            abort_unless(Storage::disk('local')->exists($report->file_path), 409, 'ملف التقرير غير متاح؛ لا يمكن تسجيل مراجعته.');
            abort_unless(hash_equals($report->file_sha256, hash('sha256', Storage::disk('local')->get($report->file_path))), 409, 'تعذر التحقق من سلامة الملف.');
            $report->update(['review_note' => $data['review_note'], 'reviewed_at' => now()]);
            $this->audit($report, 'external_reviewed');
            $report->order->doctorVisit->synchronizeWorkflowStatus();
        });

        return $this->saved($request, $doctorExternalReport, 'سُجلت مراجعتك للتقرير الخارجي، دون تحويله إلى نتيجة معتمدة من THERA.');
    }

    public function download(Request $request, DoctorExternalReport $doctorExternalReport): StreamedResponse
    {
        $this->authorizeOrder($request, $doctorExternalReport->order);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($doctorExternalReport->file_path), 404);
        abort_unless(hash_equals($doctorExternalReport->file_sha256, hash('sha256', $disk->get($doctorExternalReport->file_path))), 409, 'تعذر التحقق من سلامة الملف.');
        $extension = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'][$doctorExternalReport->file_mime] ?? null;
        abort_unless($extension, 404);
        $this->audit($doctorExternalReport, 'external_downloaded');

        return $disk->download($doctorExternalReport->file_path, 'external-report-'.$doctorExternalReport->id.'.'.$extension, ['Content-Type' => $doctorExternalReport->file_mime, 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store', 'Content-Security-Policy' => "sandbox; default-src 'none'"]);
    }

    private function authorizeOrder(Request $request, DoctorDiagnosticOrder $order): void
    {
        Gate::forUser($request->user('doctor'))->authorize('view', $order);
    }

    private function writable(Doctor $doctor, DoctorDiagnosticOrder $order, bool $upload = true): void
    {
        $visit = $order->doctorVisit;
        $membership = $visit->doctorAppointment->doctorFacility;
        if ($doctor->verification_status !== 'verified' || ! $order->approved_at || $order->status !== 'approved' || $visit->doctor_facility_id !== $membership->id || $membership->doctor_id !== $doctor->id || $membership->membership_status !== 'active' || ! $membership->facility->is_active || ($upload && $order->correction()->whereNotNull('approved_at')->exists())) {
            throw ValidationException::withMessages(['report_file' => 'يلزم طبيب موثق وعضوية ومنشأة نشطتان وطلب معتمد حالي. لا يمكن رفع تقرير جديد على إصدار سابق.']);
        }
    }

    private function audit(DoctorExternalReport $report, string $action): void
    {
        $report->order->revisions()->create(['doctor_id' => $report->doctor_id, 'action' => $action, 'revision' => $report->order->revision, 'snapshot' => ['external_report_id' => $report->id, 'file_sha256' => $report->file_sha256, 'source_name' => $report->source_name, 'review_note' => $action === 'external_reviewed' ? $report->review_note : null]]);
    }

    private function saved(Request $request, DoctorExternalReport $report, string $message): JsonResponse|RedirectResponse
    {
        $url = route('doctor.orders.reports.show', $report);

        return $request->expectsJson() ? response()->json(compact('url', 'message')) : redirect($url)->with('status', $message);
    }
}
