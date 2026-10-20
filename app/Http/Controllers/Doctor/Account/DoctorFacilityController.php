<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Account\StoreDoctorFacilityMembershipRequest;
use App\Http\Requests\Doctor\Account\UpdateDoctorFacilityRequest;
use App\Models\Doctor;
use App\Models\DoctorFacility;
use Illuminate\Http\RedirectResponse;

class DoctorFacilityController extends Controller
{
    public function store(StoreDoctorFacilityMembershipRequest $request): RedirectResponse
    {
        $doctor = $request->user('doctor');

        abort_unless($doctor instanceof Doctor, 403);

        $doctor->facilityMemberships()->create([
            ...$request->safe()->only(['facility_id', 'role']),
            'membership_status' => DoctorFacility::STATUS_PENDING,
            'is_public' => false,
        ]);

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'facilities'])
            ->with('status', 'تم إرسال طلب الانضمام إلى المنشأة للمراجعة.');
    }

    public function update(
        UpdateDoctorFacilityRequest $request,
        DoctorFacility $doctorFacility,
    ): RedirectResponse {
        $doctor = $request->user('doctor');

        abort_unless(
            $doctor instanceof Doctor && $doctorFacility->doctor_id === $doctor->id,
            404,
        );

        if ($doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE) {
            return $this->redirectWithError('يمكن تغيير الظهور العام للعضويات النشطة فقط.');
        }

        $doctorFacility->update($request->safe()->only('is_public'));

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'facilities'])
            ->with('status', 'تم تحديث ظهور المنشأة في الملف العام.');
    }

    public function destroy(DoctorFacility $doctorFacility): RedirectResponse
    {
        $doctor = request()->user('doctor');

        abort_unless(
            $doctor instanceof Doctor && $doctorFacility->doctor_id === $doctor->id,
            404,
        );

        if ($doctorFacility->membership_status !== DoctorFacility::STATUS_PENDING) {
            return $this->redirectWithError('يمكن إلغاء طلبات الانضمام التي ما زالت قيد المراجعة فقط.');
        }

        $doctorFacility->delete();

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'facilities'])
            ->with('status', 'تم إلغاء طلب الانضمام إلى المنشأة.');
    }

    private function redirectWithError(string $message): RedirectResponse
    {
        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'facilities'])
            ->withErrors(['facility_id' => $message], 'facilityMembership');
    }
}
