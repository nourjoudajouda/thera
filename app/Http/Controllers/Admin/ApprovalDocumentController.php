<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorAchievement;
use App\Models\DoctorCredential;
use App\Models\DoctorVerificationRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApprovalDocumentController extends Controller
{
    public function credential(DoctorCredential $doctorCredential): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($doctorCredential->document_path), 404);

        return Storage::disk('local')->download(
            $doctorCredential->document_path,
            $doctorCredential->document_original_name,
        );
    }

    public function achievement(DoctorAchievement $doctorAchievement): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($doctorAchievement->evidence_path), 404);

        return Storage::disk('local')->download(
            $doctorAchievement->evidence_path,
            $doctorAchievement->evidence_original_name,
        );
    }

    public function verification(DoctorVerificationRequest $verificationRequest, string $documentType): StreamedResponse
    {
        abort_unless(array_key_exists($documentType, DoctorVerificationRequest::requiredDocumentLabels()), 404);

        $document = $verificationRequest->documents[$documentType] ?? null;
        abort_unless(is_array($document) && Storage::disk('local')->exists($document['path']), 404);

        return Storage::disk('local')->download($document['path'], $document['original_name']);
    }
}
