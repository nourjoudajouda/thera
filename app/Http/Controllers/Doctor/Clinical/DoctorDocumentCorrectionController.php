<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorFacility;
use App\Models\DoctorReferral;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DoctorDocumentCorrectionController extends Controller
{
    public function order(Request $request, DoctorDiagnosticOrder $doctorDiagnosticOrder): JsonResponse|RedirectResponse
    {
        return $this->correct($request, $doctorDiagnosticOrder, 'doctor.orders.show');
    }

    public function referral(Request $request, DoctorReferral $doctorReferral): JsonResponse|RedirectResponse
    {
        return $this->correct($request, $doctorReferral, 'doctor.clinical.referrals.show');
    }

    private function correct(Request $request, DoctorDiagnosticOrder|DoctorReferral $document, string $route): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $document);
        $data = $request->validate(['reason' => ['required', 'string', 'min:10', 'max:1000'], 'revision' => ['required', 'integer', 'min:1']], ['reason.required' => 'اكتب سبب إنشاء إصدار تصحيحي.', 'reason.min' => 'وضح سبب التصحيح بعشرة أحرف على الأقل.']);
        if (! trim($data['reason'])) {
            throw ValidationException::withMessages(['reason' => 'اكتب سبب إنشاء إصدار تصحيحي.']);
        }
        $correction = DB::transaction(function () use ($request, $document, $data): DoctorDiagnosticOrder|DoctorReferral {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $document->refresh();
            abort_unless($document->approved_at && $document->status === 'approved' && is_array($document->issued_snapshot), 409, 'التصحيح يبدأ من مستند معتمد فقط.');
            if ($document->revision !== (int) $data['revision']) {
                throw ValidationException::withMessages(['revision' => 'تغير الإصدار. أعد فتح المستند قبل التصحيح.']);
            }
            $visit = $document->doctorVisit;
            $visit->loadMissing('doctorAppointment.doctorFacility.facility');
            $appointment = $visit->doctorAppointment;
            if ($doctor->verification_status !== 'verified' || ! $appointment->arrived_at || ! in_array($appointment->status, ['confirmed', 'completed'], true) || $appointment->doctor_facility_id !== $visit->doctor_facility_id || $appointment->doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $appointment->doctorFacility->facility->is_active) {
                throw ValidationException::withMessages(['revision' => 'التصحيح يتطلب طبيبًا موثقًا وحضورًا مسجلًا وعضوية ومنشأة نشطتين وموعدًا مؤكدًا أو مكتملًا.']);
            }
            if ($existing = $document->correction()->first()) {
                return $existing;
            }
            $attributes = $document->only(['doctor_id', 'doctor_visit_id', 'kind', 'priority', 'content']);
            if ($document instanceof DoctorReferral) {
                $attributes += $document->only(['target_doctor_id', 'target_facility_id']);
            }
            $child = $document->newQuery()->create($attributes + ['status' => 'draft', 'revision' => 1, 'request_key' => (string) Str::uuid(), 'corrects_document_id' => $document->id, 'document_version' => $document->document_version + 1, 'correction_reason' => trim($data['reason'])]);
            $child->revisions()->create(['doctor_id' => $doctor->id, 'action' => 'correction_created', 'revision' => 1, 'snapshot' => ['source_id' => $document->id, 'source_revision' => $document->revision, 'document_version' => $child->document_version, 'reason' => $child->correction_reason, 'content' => $child->content, 'status' => 'draft']]);

            return $child;
        }, 3);
        $url = route($route, $correction);
        $message = 'فُتحت مسودة تصحيح مرتبطة بالأصل. الأصل يبقى المعتمد حتى اعتماد التصحيح، ولا تتغير حالة الزيارة أو التحصيل.';

        return $request->expectsJson() ? response()->json(compact('url', 'message')) : redirect($url)->with('status', $message);
    }
}
