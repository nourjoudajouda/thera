<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientDelegation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatientDelegationController extends Controller
{
    public function index(): View
    {
        return view('admin.patient-delegations', ['records' => PatientDelegation::where('kind', 'guardian')->with(['patient', 'user'])->orderByDesc('id')->paginate(20)]);
    }

    public function proof(PatientDelegation $delegation): BinaryFileResponse
    {
        abort_unless($delegation->proof_path && Storage::disk('local')->exists($delegation->proof_path), 404);
        $delegation->patient->recordAccess('guardian_proof_read', adminId: auth('admin')->id(), detail: ['delegation_id' => $delegation->id]);

        return response()->file(Storage::disk('local')->path($delegation->proof_path), ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function update(Request $request, PatientDelegation $delegation): RedirectResponse
    {
        $data = $request->validate(['action' => ['required', 'in:approve,reject,revoke'], 'reason' => ['required', 'string', 'max:500'], 'expires_at' => ['required_if:action,approve', 'nullable', 'date', 'after:now', 'before_or_equal:'.now()->addYear()->toDateString()]]);
        DB::transaction(function () use ($request, $delegation, $data): void {
            $patient = Patient::lockForUpdate()->findOrFail($delegation->patient_id);
            $delegation->refresh();
            abort_unless($delegation->kind === 'guardian', 409);
            if ($data['action'] !== 'revoke') {
                abort_unless($delegation->status === 'pending', 409);
            }
            abort_if($data['action'] === 'approve' && (! $patient->birth_date || $patient->birth_date->age >= 18), 422, 'يحتاج ملف البالغ موافقة صاحبه.');
            $delegation->update(['status' => ['approve' => 'approved', 'reject' => 'rejected', 'revoke' => 'revoked'][$data['action']], 'reviewed_by' => $request->user('admin')->id, 'review_reason' => $data['reason'], 'expires_at' => $data['action'] === 'approve' ? $data['expires_at'] : $delegation->expires_at]);
            if ($data['action'] === 'approve' && ! $patient->user_id) {
                $guardian = Patient::where('user_id', $delegation->user_id)->whereNotNull('phone_verified_at')->firstOrFail();
                $patient->update(['booking_enabled' => true, 'phone_verified_at' => $guardian->phone_verified_at]);
            }
            $patient->recordAccess('guardian_'.$data['action'], adminId: $request->user('admin')->id, detail: ['delegation_id' => $delegation->id, 'reason' => $data['reason']]);
        });

        return back()->with('status', 'تم حفظ قرار التفويض.');
    }
}
