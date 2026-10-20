<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Account\StoreDoctorCredentialRequest;
use App\Models\Doctor;
use App\Models\DoctorCredential;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DoctorCredentialController extends Controller
{
    public function store(StoreDoctorCredentialRequest $request): RedirectResponse
    {
        $doctor = $request->user('doctor');

        abort_unless($doctor instanceof Doctor, 403);

        $document = $request->file('document');
        abort_unless($document !== null, 422);

        $path = $document->store("doctor-documents/{$doctor->id}/credentials", 'local');
        abort_unless(is_string($path), 500, 'تعذر حفظ ملف إثبات المؤهل.');

        try {
            $doctor->credentials()->create([
                ...$request->safe()->except('document'),
                'document_path' => $path,
                'document_original_name' => $document->getClientOriginalName(),
                'verification_status' => DoctorCredential::STATUS_PENDING,
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'credentials'])
            ->with('status', 'تمت إضافة المؤهل وإرساله للمراجعة.');
    }

    public function download(Request $request, DoctorCredential $doctorCredential): StreamedResponse
    {
        $this->ensureOwnership($request, $doctorCredential);

        abort_unless(Storage::disk('local')->exists($doctorCredential->document_path), 404);

        return Storage::disk('local')->download(
            $doctorCredential->document_path,
            $doctorCredential->document_original_name,
        );
    }

    public function destroy(Request $request, DoctorCredential $doctorCredential): RedirectResponse
    {
        $this->ensureOwnership($request, $doctorCredential);

        if ($doctorCredential->verification_status === DoctorCredential::STATUS_VERIFIED) {
            return redirect()
                ->route('doctor.account.professional-profile', ['tab' => 'credentials'])
                ->withErrors(['credential' => 'لا يمكن حذف مؤهل موثّق. تواصل مع الدعم لتعديله.']);
        }

        $path = $doctorCredential->document_path;
        $doctorCredential->delete();
        Storage::disk('local')->delete($path);

        return redirect()
            ->route('doctor.account.professional-profile', ['tab' => 'credentials'])
            ->with('status', 'تم حذف المؤهل.');
    }

    private function ensureOwnership(Request $request, DoctorCredential $doctorCredential): void
    {
        $doctor = $request->user('doctor');

        abort_unless(
            $doctor instanceof Doctor && $doctorCredential->doctor_id === $doctor->id,
            404,
        );
    }
}
