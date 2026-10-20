<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MedicationApprovalController extends Controller
{
    public function update(Request $request, Medication $medication): RedirectResponse
    {
        $data = $request->validate(['decision' => ['required', Rule::in(['approve', 'reject'])], 'rejection_reason' => ['required_if:decision,reject', 'nullable', 'string', 'max:2000']], ['rejection_reason.required_if' => 'اذكر سبب رفض تعريف الدواء.']);
        DB::transaction(function () use ($request, $medication, $data): void {
            $medication = Medication::query()->lockForUpdate()->findOrFail($medication->id);
            abort_unless($medication->status === 'pending', 409, 'تمت مراجعة الدواء مسبقًا.');
            $medication->update(['status' => $data['decision'] === 'approve' ? 'approved' : 'rejected', 'decided_by' => $request->user('admin')->id, 'decided_at' => now('UTC'), 'rejection_reason' => $data['decision'] === 'reject' ? $data['rejection_reason'] : null]);
        });

        return redirect()->route('admin.approvals.index')->with('status', 'حُفظ قرار مراجعة تعريف الدواء؛ هذا لا يعتمد وصفة أو جرعة علاجية.');
    }
}
