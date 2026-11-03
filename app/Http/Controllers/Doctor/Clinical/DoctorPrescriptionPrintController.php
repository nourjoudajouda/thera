<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Http\Controllers\Controller;
use App\Models\DoctorPrescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DoctorPrescriptionPrintController extends Controller
{
    public function show(Request $request, DoctorPrescription $doctorPrescription): Response
    {
        $this->authorizeDocument($request, $doctorPrescription);
        $doctorPrescription->load('correction:id,corrects_prescription_id,approved_at');
        $this->audit($request, $doctorPrescription, 'print_previewed');

        return response()->view('doctor.clinical.prescription-print', ['prescription' => $doctorPrescription, 'snapshot' => $doctorPrescription->issued_snapshot, 'stamps' => $request->user('doctor')->stamps()->latest('id')->get()])
            ->header('Cache-Control', 'private, no-store')->header('X-Robots-Tag', 'noindex, nofollow')->header('Referrer-Policy', 'no-referrer');
    }

    public function store(Request $request, DoctorPrescription $doctorPrescription): JsonResponse
    {
        $this->authorizeDocument($request, $doctorPrescription);
        $selection = (new PrintStampSelection)->resolve($request, $request->user('doctor'), $doctorPrescription->issued_snapshot);
        $this->audit($request, $doctorPrescription, 'print_requested', $selection);

        return response()->json(['message' => 'سُجل طلب فتح نافذة الطباعة، وليس تأكيد الطباعة الفعلية.', 'stamp_data_uri' => $selection['stamp_data_uri']])->header('Cache-Control', 'private, no-store');
    }

    private function authorizeDocument(Request $request, DoctorPrescription $prescription): void
    {
        Gate::forUser($request->user('doctor'))->authorize('view', $prescription);
        abort_unless($prescription->status === 'approved' && $prescription->approved_at && is_array($prescription->issued_snapshot), 409, 'الطباعة متاحة للوصفة المعتمدة فقط.');
    }

    /** @param array<string, mixed>|null $selection */
    private function audit(Request $request, DoctorPrescription $prescription, string $action, ?array $selection = null): void
    {
        $prescription->revisions()->create(['doctor_id' => $request->user('doctor')->id, 'action' => $action, 'revision' => $prescription->revision, 'snapshot' => $selection ? ['print_stamp' => $selection] : null]);
    }
}
