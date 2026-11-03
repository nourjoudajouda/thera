<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DoctorExternalReport;
use App\Models\Patient;
use App\Services\PatientRecordReader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatientRecordController extends Controller
{
    private function patient(Request $request): Patient
    {
        return Patient::accessibleTo($request->user())->where('public_id', $request->route('patient'))->firstOrFail();
    }

    public function index(Request $request, PatientRecordReader $reader): JsonResponse
    {
        $data = $request->validate(['page' => ['nullable', 'integer', 'min:1']]);
        $patient = $this->patient($request);
        $patient->recordAccess('medical_timeline_read', $request->user()->id);
        $rows = collect($reader->records($patient));
        $page = (int) ($data['page'] ?? 1);

        return response()->json(['data' => $rows->forPage($page, 25)->values(), 'meta' => ['current_page' => $page, 'last_page' => max(1, (int) ceil($rows->count() / 25))]])->header('Cache-Control', 'private, no-store');
    }

    public function show(Request $request, string $patient, string $type, int $id, PatientRecordReader $reader): JsonResponse
    {
        $record = $this->patient($request);
        $document = $reader->record($record, $type, $id);
        $record->recordAccess('medical_document_read', $request->user()->id, detail: ['document' => $document['key']]);

        return response()->json(['data' => $document])->header('Cache-Control', 'private, no-store');
    }

    public function externalReport(Request $request, string $patient, int $id, PatientRecordReader $reader): BinaryFileResponse
    {
        $record = $this->patient($request);
        $reader->record($record, 'report', $id);
        $report = DoctorExternalReport::findOrFail($id);
        $record->recordAccess('external_report_downloaded', $request->user()->id, detail: ['report_id' => $id]);
        abort_unless(Storage::disk('local')->exists($report->file_path), 404);

        return response()->download(Storage::disk('local')->path($report->file_path), 'THERA-report-'.$id.'.'.($report->file_mime === 'application/pdf' ? 'pdf' : ($report->file_mime === 'image/png' ? 'png' : 'jpg')), ['Content-Type' => $report->file_mime, 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function pdf(Request $request, string $patient, string $type, int $id, PatientRecordReader $reader): Response
    {
        $record = $this->patient($request);
        $document = $reader->record($record, $type, $id);
        abort_if($type === 'report', 422, 'استخدم الملف الأصلي للتقرير.');
        $pdf = $reader->pdf($record, $document);
        $record->recordAccess('medical_document_downloaded', $request->user()->id, detail: ['document' => $document['key']]);

        return response($pdf)->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'attachment; filename="THERA-'.$type.'-'.$id.'.pdf"')->header('Cache-Control', 'private, no-store');
    }
}
