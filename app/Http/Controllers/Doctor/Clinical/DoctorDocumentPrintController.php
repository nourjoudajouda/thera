<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Http\Controllers\Controller;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorReferral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DoctorDocumentPrintController extends Controller
{
    public function showOrder(Request $request, DoctorDiagnosticOrder $doctorDiagnosticOrder): Response
    {
        return $this->preview($request, $doctorDiagnosticOrder, 'طلب '.DoctorDiagnosticOrder::KINDS[$doctorDiagnosticOrder->kind], 'doctor.orders');
    }

    public function storeOrder(Request $request, DoctorDiagnosticOrder $doctorDiagnosticOrder): JsonResponse
    {
        return $this->requestPrint($request, $doctorDiagnosticOrder);
    }

    public function showReferral(Request $request, DoctorReferral $doctorReferral): Response
    {
        return $this->preview($request, $doctorReferral, DoctorReferral::KINDS[$doctorReferral->kind], 'doctor.clinical.referrals');
    }

    public function storeReferral(Request $request, DoctorReferral $doctorReferral): JsonResponse
    {
        return $this->requestPrint($request, $doctorReferral);
    }

    private function preview(Request $request, DoctorDiagnosticOrder|DoctorReferral $document, string $title, string $routePrefix): Response
    {
        $this->authorizeDocument($request, $document);
        $document->load(['correctedFrom', 'correction']);
        $this->audit($request, $document, 'print_previewed');

        return response()->view('doctor.clinical.document-print', ['document' => $document, 'title' => $title, 'snapshot' => $document->issued_snapshot, 'stamps' => $request->user('doctor')->stamps()->latest('id')->get(), 'isOrder' => $document instanceof DoctorDiagnosticOrder, 'backUrl' => route($routePrefix.'.show', $document), 'printUrl' => route($routePrefix.'.print.store', $document)])
            ->header('Cache-Control', 'private, no-store')->header('X-Robots-Tag', 'noindex, nofollow')->header('Referrer-Policy', 'no-referrer');
    }

    private function requestPrint(Request $request, DoctorDiagnosticOrder|DoctorReferral $document): JsonResponse
    {
        $this->authorizeDocument($request, $document);
        $selection = (new PrintStampSelection)->resolve($request, $request->user('doctor'), $document->issued_snapshot);
        $this->audit($request, $document, 'print_requested', $selection);

        return response()->json(['message' => 'سُجل طلب فتح نافذة الطباعة، وليس تأكيد الطباعة الفعلية.', 'stamp_data_uri' => $selection['stamp_data_uri']])->header('Cache-Control', 'private, no-store');
    }

    private function authorizeDocument(Request $request, DoctorDiagnosticOrder|DoctorReferral $document): void
    {
        Gate::forUser($request->user('doctor'))->authorize('view', $document);
        abort_unless($document->status === 'approved' && $document->approved_at && is_array($document->issued_snapshot), 409, 'الطباعة متاحة للمستند المعتمد فقط.');
    }

    /** @param array<string, mixed>|null $selection */
    private function audit(Request $request, DoctorDiagnosticOrder|DoctorReferral $document, string $action, ?array $selection = null): void
    {
        $document->revisions()->create(['doctor_id' => $request->user('doctor')->id, 'action' => $action, 'revision' => $document->revision, 'snapshot' => $selection ? ['print_stamp' => $selection] : null]);
    }
}
