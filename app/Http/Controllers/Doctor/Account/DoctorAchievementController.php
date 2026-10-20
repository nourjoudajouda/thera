<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Account\StoreDoctorAchievementRequest;
use App\Models\Doctor;
use App\Models\DoctorAchievement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DoctorAchievementController extends Controller
{
    public function store(StoreDoctorAchievementRequest $request): RedirectResponse
    {
        $doctor = $request->user('doctor');

        abort_unless($doctor instanceof Doctor, 403);

        $evidence = $request->file('evidence');
        abort_unless($evidence !== null, 422);

        $path = $evidence->store("doctor-documents/{$doctor->id}/achievements", 'local');
        abort_unless(is_string($path), 500, 'تعذر حفظ ملف إثبات الإنجاز.');

        try {
            $doctor->achievements()->create([
                ...$request->safe()->except('evidence'),
                'evidence_path' => $path,
                'evidence_original_name' => $evidence->getClientOriginalName(),
                'verification_status' => DoctorAchievement::STATUS_PENDING,
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'achievements'])
            ->with('status', 'تمت إضافة الإنجاز وإرساله للمراجعة.');
    }

    public function download(Request $request, DoctorAchievement $doctorAchievement): StreamedResponse
    {
        $this->ensureOwnership($request, $doctorAchievement);

        abort_unless(Storage::disk('local')->exists($doctorAchievement->evidence_path), 404);

        return Storage::disk('local')->download(
            $doctorAchievement->evidence_path,
            $doctorAchievement->evidence_original_name,
        );
    }

    public function destroy(Request $request, DoctorAchievement $doctorAchievement): RedirectResponse
    {
        $this->ensureOwnership($request, $doctorAchievement);

        if ($doctorAchievement->verification_status === DoctorAchievement::STATUS_VERIFIED) {
            return redirect()
                ->route('doctor.account.professional-profile', ['tab' => 'achievements'])
                ->withErrors(['achievement' => 'لا يمكن حذف إنجاز موثّق. تواصل مع الدعم لتعديله.']);
        }

        $path = $doctorAchievement->evidence_path;
        $doctorAchievement->delete();
        Storage::disk('local')->delete($path);

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'achievements'])
            ->with('status', 'تم حذف الإنجاز.');
    }

    private function ensureOwnership(Request $request, DoctorAchievement $doctorAchievement): void
    {
        $doctor = $request->user('doctor');

        abort_unless(
            $doctor instanceof Doctor && $doctorAchievement->doctor_id === $doctor->id,
            404,
        );
    }
}
