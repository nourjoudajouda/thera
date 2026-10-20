<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Account\StoreDoctorSpecialtyRequest;
use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DoctorSpecialtyController extends Controller
{
    public function store(StoreDoctorSpecialtyRequest $request): RedirectResponse
    {
        $doctor = $request->user('doctor');

        abort_unless($doctor instanceof Doctor, 403);

        $validated = $request->validated();

        DB::transaction(function () use ($doctor, $validated): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($doctor->id);
            if ($doctor->verificationRequests()->where('status', 'submitted')->exists()) {
                throw ValidationException::withMessages(['specialty_id' => 'لا يمكن تعديل التخصصات أثناء مراجعة طلب التحقق.']);
            }
            if ($validated['type'] === DoctorSpecialty::TYPE_PRIMARY) {
                $doctor->specialtyAssignments()
                    ->whereIn('type', [DoctorSpecialty::TYPE_PRIMARY, DoctorSpecialty::TYPE_SUBSPECIALTY])
                    ->delete();
            }

            $doctor->specialtyAssignments()->create([
                'specialty_id' => $validated['specialty_id'],
                'type' => $validated['type'],
                'verification_status' => DoctorSpecialty::STATUS_PENDING,
            ]);
        });

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'specialties'])
            ->with('status', $validated['type'] === DoctorSpecialty::TYPE_PRIMARY
                ? 'تم حفظ التخصص الرئيسي وإرساله للمراجعة.'
                : 'تمت إضافة التخصص الفرعي وإرساله للمراجعة.');
    }

    public function destroy(DoctorSpecialty $doctorSpecialty): RedirectResponse
    {
        $doctor = request()->user('doctor');

        abort_unless(
            $doctor instanceof Doctor && $doctorSpecialty->doctor_id === $doctor->id,
            404,
        );

        if ($doctorSpecialty->type === DoctorSpecialty::TYPE_PRIMARY) {
            return $this->redirectWithError('لا يمكن حذف التخصص الرئيسي. يمكنك تغييره من قائمة التخصصات الرئيسية.');
        }

        if ($doctorSpecialty->verification_status === DoctorSpecialty::STATUS_VERIFIED) {
            return $this->redirectWithError('لا يمكن حذف تخصص موثّق. تواصل مع الدعم لتعديله.');
        }

        $doctorSpecialty->delete();

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'specialties'])
            ->with('status', 'تم حذف التخصص الفرعي.');
    }

    private function redirectWithError(string $message): RedirectResponse
    {
        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'specialties'])
            ->withErrors(['specialty_id' => $message]);
    }
}
