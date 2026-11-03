<?php

namespace App\Services;

use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPrescription;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use App\Models\Patient;
use Mpdf\Mpdf;

class PatientRecordReader
{
    public function pdf(Patient $patient, array $document): string
    {
        $pdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'autoScriptToLang' => true, 'autoLangToFont' => true, 'tempDir' => storage_path('framework/cache')]);
        $pdf->WriteHTML(view('patient.document-pdf', compact('patient', 'document'))->render());

        return $pdf->Output('', 'S');
    }

    public function records(Patient $patient): array
    {
        $rows = [];
        foreach (DoctorVisit::whereHas('doctorAppointment.patient', fn ($q) => $q->where('patient_id', $patient->id))->whereNotNull('closed_at')->with(['doctor', 'doctorAppointment'])->get() as $visit) {
            $rows[] = ['key' => 'visit:'.$visit->id, 'type' => 'visit', 'id' => $visit->id, 'title' => 'ملخص الزيارة', 'patient_name' => $visit->doctorAppointment->patient_name, 'date' => $visit->closed_at->toIso8601String(), 'doctor_name' => $visit->doctor->name, 'version' => 1, 'content' => $visit->content, 'stamp_data_uri' => null];
        }
        foreach (['prescription' => DoctorPrescription::class, 'order' => DoctorDiagnosticOrder::class, 'referral' => DoctorReferral::class] as $type => $class) {
            $documents = $class::whereHas('doctorVisit.doctorAppointment.patient', fn ($q) => $q->where('patient_id', $patient->id))->where('status', 'approved')->whereNotNull('approved_at')->with(['doctor', 'correction'])->get();
            foreach ($documents as $document) {
                $snapshot = $document->issued_snapshot ?? [];
                $rows[] = ['key' => $type.':'.$document->id, 'type' => $type, 'id' => $document->id, 'title' => ['prescription' => 'وصفة طبية', 'order' => 'طلب فحص', 'referral' => 'إحالة أو متابعة'][$type], 'date' => $document->approved_at->toIso8601String(), 'logo_data_uri' => $snapshot['logo_data_uri'] ?? null, 'doctor_name' => $snapshot['doctor_name'] ?? $document->doctor->name, 'patient_name' => $snapshot['patient_name'] ?? $patient->name, 'version' => $document->document_version, 'content' => $snapshot['content'] ?? $document->content, 'stamp_data_uri' => $snapshot['stamp_data_uri'] ?? null, 'corrected_by' => $document->correction?->approved_at ? $document->correction->id : null];
            }
        }
        foreach (DoctorExternalReport::whereHas('order.doctorVisit.doctorAppointment.patient', fn ($q) => $q->where('patient_id', $patient->id))->whereNotNull('reviewed_at')->with('order.doctor')->get() as $report) {
            $rows[] = ['key' => 'report:'.$report->id, 'file_mime' => $report->file_mime, 'type' => 'report', 'id' => $report->id, 'title' => $report->title, 'date' => $report->reviewed_at->toIso8601String(), 'doctor_name' => $report->order->doctor->name, 'version' => 1, 'content' => ['source' => $report->source_name, 'report_date' => $report->report_date?->format('Y-m-d'), 'review_note' => $report->review_note, 'status' => 'تقرير خارجي راجعه الطبيب'], 'stamp_data_uri' => null];
        }

        return collect($rows)->sortByDesc('date')->values()->map(fn ($r) => [...$r, 'display_text' => $this->contentText($r['content'] ?? [])])->all();
    }

    public function record(Patient $patient, string $type, int $id): array
    {
        $record = collect($this->records($patient))->first(fn ($r) => $r['type'] === $type && $r['id'] === $id);
        abort_unless($record, 404);

        return $record;
    }

    private function contentText(mixed $content): string
    {
        if (! is_array($content)) {
            return is_scalar($content) ? (string) $content : '';
        }
        $labels = ['assessment' => 'التقييم', 'diagnosis' => 'التشخيص', 'diagnosis_type' => 'نوع التشخيص', 'plan' => 'الخطة', 'chief_complaint' => 'الشكوى', 'symptoms' => 'الأعراض', 'notes' => 'ملاحظات', 'instructions' => 'تعليمات', 'name' => 'الاسم', 'dosage' => 'الجرعة', 'dose' => 'الجرعة', 'frequency' => 'التكرار', 'duration' => 'المدة', 'quantity' => 'الكمية', 'route' => 'طريقة الاستخدام', 'reason' => 'السبب', 'source' => 'المصدر', 'review_note' => 'مراجعة الطبيب', 'report_date' => 'تاريخ التقرير', 'status' => 'الحالة', 'allergies' => 'الحساسيات', 'medications' => 'الأدوية', 'follow_up' => 'المتابعة'];
        $lines = [];
        foreach ($content as $key => $value) {
            if (in_array($key, ['id', 'medication_id', 'doctor_id', 'catalog_id'], true)) {
                continue;
            }
            $text = $this->contentText($value);
            if (trim($text) !== '') {
                $lines[] = (isset($labels[$key]) ? $labels[$key].': ' : '').$text;
            }
        }

        return implode("\n", $lines);
    }
}
