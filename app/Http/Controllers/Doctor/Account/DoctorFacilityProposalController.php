<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Account\StoreDoctorFacilityProposalRequest;
use App\Models\Doctor;
use App\Models\FacilityProposal;
use Illuminate\Http\RedirectResponse;

class DoctorFacilityProposalController extends Controller
{
    public function store(StoreDoctorFacilityProposalRequest $request): RedirectResponse
    {
        $doctor = $request->user('doctor');

        abort_unless($doctor instanceof Doctor, 403);

        $doctor->facilityProposals()->create([
            ...$request->validated(),
            'status' => FacilityProposal::STATUS_PENDING,
        ]);

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'facilities'])
            ->with('status', 'تم إرسال طلب إضافة المنشأة للمراجعة.');
    }

    public function destroy(FacilityProposal $facilityProposal): RedirectResponse
    {
        $doctor = request()->user('doctor');

        abort_unless(
            $doctor instanceof Doctor && $facilityProposal->doctor_id === $doctor->id,
            404,
        );

        if ($facilityProposal->status !== FacilityProposal::STATUS_PENDING) {
            return redirect()
                ->route('doctor.account.professional-profile', ['tab' => 'facilities'])
                ->withErrors(['name' => 'يمكن إلغاء طلبات المنشآت التي ما زالت قيد المراجعة فقط.'], 'facilityProposal');
        }

        $facilityProposal->delete();

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'facilities'])
            ->with('status', 'تم إلغاء طلب إضافة المنشأة.');
    }
}
