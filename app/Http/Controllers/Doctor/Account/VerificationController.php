<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Account\StoreVerificationDocumentRequest;
use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use App\Models\DoctorVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class VerificationController extends Controller
{
    public function show(Request $request): View
    {
        $doctor = $this->doctor($request);
        $doctor->load(['primarySpecialtyAssignment.specialty']);
        $doctor->loadCount(['credentials', 'achievements']);

        $draftRequest = $doctor->verificationRequests()
            ->where('status', DoctorVerificationRequest::STATUS_DRAFT)
            ->latest()
            ->first();
        $latestRequest = $doctor->verificationRequests()
            ->where('status', '!=', DoctorVerificationRequest::STATUS_DRAFT)
            ->latest('submitted_at')
            ->first();
        $verificationRequest = $draftRequest ?? $latestRequest;
        $documents = collect(DoctorVerificationRequest::requiredDocumentLabels())
            ->map(function (string $label, string $type) use ($verificationRequest): array {
                $document = $verificationRequest?->documents[$type] ?? null;

                return [
                    'type' => $type,
                    'label' => $label,
                    'uploaded' => is_array($document),
                    'original_name' => is_array($document) ? $document['original_name'] : null,
                    'download_url' => is_array($document) && $verificationRequest
                        ? route('doctor.account.verification.documents.download', [$verificationRequest, $type])
                        : null,
                ];
            })
            ->values();

        return view('doctor.account.verification', [
            'doctor' => $doctor,
            'draftRequest' => $draftRequest,
            'latestRequest' => $latestRequest,
            'verificationRequest' => $verificationRequest,
            'requiredDocuments' => DoctorVerificationRequest::requiredDocumentLabels(),
            'verificationDocuments' => $documents,
            'profileIsComplete' => $this->profileIsComplete($doctor),
            'verificationHistory' => $doctor->verificationRequests()
                ->with('decidedBy:id,name')
                ->whereNotNull('submitted_at')
                ->orderByDesc('submitted_at')->orderByDesc('id')->get(),
        ]);
    }

    public function storeDocument(StoreVerificationDocumentRequest $request): RedirectResponse
    {
        $doctor = $this->doctor($request);

        $path = null;
        $oldPath = null;
        try {
            DB::transaction(function () use ($doctor, $request, &$path, &$oldPath): void {
                $doctor = Doctor::query()->lockForUpdate()->findOrFail($doctor->id);
                $this->ensureCanApply($doctor);
                $verificationRequest = $this->draft($doctor);
                $documentType = $request->string('document_type')->toString();
                $file = $request->file('document');
                $path = $file->store("doctor-verification-documents/{$doctor->id}", 'local');
                abort_unless(is_string($path), 500, 'تعذر حفظ المستند.');
                $documents = $verificationRequest->documents ?? [];
                $oldPath = $documents[$documentType]['path'] ?? null;
                $documents[$documentType] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toISOString(),
                ];
                $verificationRequest->update(['documents' => $documents]);
            });
        } catch (Throwable $exception) {
            if (is_string($path)) {
                Storage::disk('local')->delete($path);
            }
            throw $exception;
        }

        if (is_string($oldPath) && $oldPath !== $path) {
            $stillReferenced = $doctor->verificationRequests()->get(['documents'])
                ->contains(fn (DoctorVerificationRequest $item): bool => collect($item->documents ?? [])->contains('path', $oldPath));
            if (! $stillReferenced) {
                Storage::disk('local')->delete($oldPath);
            }
        }

        return redirect()
            ->route('doctor.account.verification', ['onboarding' => 1, 'tab' => 'documents'])
            ->with('status', 'تم رفع المستند بنجاح.');
    }

    public function submit(Request $request): RedirectResponse
    {
        $doctor = $this->doctor($request);

        DB::transaction(function () use ($doctor): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($doctor->id);
            $this->ensureCanApply($doctor);
            $verificationRequest = $doctor->verificationRequests()
                ->where('status', DoctorVerificationRequest::STATUS_DRAFT)
                ->lockForUpdate()
                ->latest()
                ->first();

            if (! $verificationRequest) {
                throw ValidationException::withMessages(['verification' => 'ارفع المستندات المطلوبة قبل إرسال الطلب.']);
            }

            if (! $this->profileIsComplete($doctor)) {
                throw ValidationException::withMessages(['verification' => 'أكمل الملف المهني والتخصص الرئيسي قبل إرسال الطلب.']);
            }

            if (! $verificationRequest->hasAllRequiredDocuments()) {
                throw ValidationException::withMessages(['verification' => 'ارفع جميع المستندات المطلوبة قبل إرسال الطلب.']);
            }

            if ($doctor->verificationRequests()
                ->where('status', DoctorVerificationRequest::STATUS_SUBMITTED)
                ->whereKeyNot($verificationRequest->id)
                ->exists()) {
                throw ValidationException::withMessages(['verification' => 'يوجد طلب تحقق قيد المراجعة بالفعل.']);
            }

            $verificationRequest->update([
                'reference' => 'VR-'.now()->format('Y').'-'.str_pad((string) $verificationRequest->id, 6, '0', STR_PAD_LEFT),
                'status' => DoctorVerificationRequest::STATUS_SUBMITTED,
                'profile_snapshot' => [
                    ...$doctor->only(['name', 'email', 'phone', 'professional_title', 'years_experience', 'professional_bio', 'bio', 'languages']),
                    'primary_specialty_id' => $doctor->primarySpecialtyAssignment->specialty_id,
                    'primary_specialty_name' => $doctor->primarySpecialtyAssignment->specialty->name_ar,
                ],
                'submitted_at' => now(),
                'decided_at' => null,
                'decided_by' => null,
                'rejection_reason' => null,
            ]);
            $doctor->update(['verification_status' => 'pending']);
        });

        return redirect()
            ->route('doctor.account.verification', ['tab' => 'submission'])
            ->with('status', 'تم إرسال طلب التحقق إلى الإدارة بنجاح.');
    }

    public function download(Request $request, DoctorVerificationRequest $verificationRequest, string $documentType): StreamedResponse
    {
        $doctor = $this->doctor($request);

        abort_unless($verificationRequest->doctor_id === $doctor->id, 404);
        abort_unless(array_key_exists($documentType, DoctorVerificationRequest::requiredDocumentLabels()), 404);

        $document = $verificationRequest->documents[$documentType] ?? null;
        abort_unless(is_array($document) && Storage::disk('local')->exists($document['path']), 404);

        return Storage::disk('local')->download($document['path'], $document['original_name']);
    }

    public function restart(Request $request): RedirectResponse
    {
        $doctor = $this->doctor($request);
        DB::transaction(function () use ($doctor): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($doctor->id);
            $this->ensureCanApply($doctor);
            $latest = $doctor->verificationRequests()->whereNotNull('submitted_at')->latest('id')->first();
            if ($latest?->status !== DoctorVerificationRequest::STATUS_REJECTED) {
                throw ValidationException::withMessages(['verification' => 'يمكن إعادة التقديم بعد رفض الطلب فقط.']);
            }
            $this->draft($doctor);
        });

        return redirect()->route('doctor.account.verification', ['tab' => 'documents'])
            ->with('status', 'تم تجهيز مسودة جديدة بالمستندات السابقة. عدّل المستندات المطلوبة ثم أعد الإرسال.');
    }

    private function ensureCanApply(Doctor $doctor): void
    {
        if ($doctor->verification_status === 'verified' || $doctor->verificationRequests()->where('status', DoctorVerificationRequest::STATUS_APPROVED)->exists()) {
            throw ValidationException::withMessages(['verification' => 'تم اعتماد حسابك بالفعل؛ لا يمكن تقديم طلب تحقق جديد.']);
        }
        if ($doctor->verificationRequests()->where('status', DoctorVerificationRequest::STATUS_SUBMITTED)->exists()) {
            throw ValidationException::withMessages(['verification' => 'يوجد طلب تحقق قيد المراجعة؛ لا يمكن تعديل المستندات أو إعادة الإرسال.']);
        }
    }

    private function draft(Doctor $doctor): DoctorVerificationRequest
    {
        $previous = $doctor->verificationRequests()->where('status', DoctorVerificationRequest::STATUS_REJECTED)->latest('id')->first();

        return $doctor->verificationRequests()->firstOrCreate(
            ['status' => DoctorVerificationRequest::STATUS_DRAFT],
            ['documents' => $previous?->documents ?? []],
        );
    }

    private function doctor(Request $request): Doctor
    {
        $doctor = $request->user('doctor');

        abort_unless($doctor instanceof Doctor, 403);

        return $doctor;
    }

    private function profileIsComplete(Doctor $doctor): bool
    {
        return filled($doctor->name)
            && filled($doctor->professional_title)
            && $doctor->years_experience !== null
            && filled($doctor->professional_bio)
            && $doctor->primarySpecialtyAssignment instanceof DoctorSpecialty;
    }
}
