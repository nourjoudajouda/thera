<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPatient;
use App\Models\Patient;
use App\Services\PatientRecordReader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatientSharedRecordController extends Controller
{
    private function patient(Request $request, DoctorPatient $doctorPatient): Patient
    {
        $doctor = $request->user('doctor');
        Gate::forUser($doctor)->authorize('view', $doctorPatient);
        $patient = Patient::findOrFail($doctorPatient->patient_id);
        abort_unless($doctor->verification_status === 'verified' && $patient->hasDoctorConsent($doctor), 403, 'تحتاج موافقة سارية من المريض لقراءة سجله السابق.');

        return $patient;
    }

    public function show(Request $request, DoctorPatient $doctorPatient, PatientRecordReader $reader): Response
    {
        $doctor = $request->user('doctor');
        $patient = $this->patient($request, $doctorPatient);
        $patient->recordAccess('shared_medical_timeline_read', doctorId: $doctor->id);

        return response()->view('doctor.patients.shared-record', ['patient' => $patient, 'doctorPatient' => $doctorPatient, 'records' => $reader->records($patient)])->header('Cache-Control', 'private, no-store')->header('Referrer-Policy', 'no-referrer');
    }

    public function download(Request $request, DoctorPatient $doctorPatient, string $type, int $id, PatientRecordReader $reader): Response|BinaryFileResponse
    {
        $patient = $this->patient($request, $doctorPatient);
        $document = $reader->record($patient, $type, $id);
        $patient->recordAccess('shared_document_downloaded', doctorId: $request->user('doctor')->id, detail: ['document' => $document['key']]);
        if ($type === 'report') {
            $report = DoctorExternalReport::findOrFail($id);
            $disk = Storage::disk('local');
            abort_unless($disk->exists($report->file_path), 404);

            return response()->download($disk->path($report->file_path), 'THERA-report-'.$id.'.'.($report->file_mime === 'application/pdf' ? 'pdf' : ($report->file_mime === 'image/png' ? 'png' : 'jpg')), ['Content-Type' => $report->file_mime, 'Cache-Control' => 'private, no-store']);
        }

        return response($reader->pdf($patient, $document))->header('Content-Type', 'application/pdf')->header('Cache-Control', 'private, no-store');
    }
}
