<?php

namespace Tests\Feature;

use App\Models\DoctorAppointment;
use App\Models\DoctorPatient;
use App\Models\DoctorPrescription;
use App\Models\DoctorService;
use App\Models\DoctorVisit;
use App\Models\Patient;
use App\Models\PatientDelegation;
use App\Models\PatientDoctorConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientRecordAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    private function patientDocument(): array
    {
        $patient = Patient::factory()->bookable()->create();
        $service = DoctorService::factory()->create();
        $doctor = $service->doctorFacility->doctor;
        $doctor->update(['verification_status' => 'verified']);
        $file = DoctorPatient::factory()->create(['doctor_id' => $doctor->id, 'patient_id' => $patient->id]);
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'doctor_patient_id' => $file->id, 'doctor_facility_id' => $service->doctor_facility_id, 'arrived_at' => now()]);
        $visit = DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id, 'status' => 'completed', 'closed_at' => now(), 'content' => ['diagnosis' => 'تشخيص تجريبي']]);
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id, 'status' => 'approved', 'approved_at' => now(), 'content' => ['items' => [['name' => 'دواء تجريبي', 'dose' => 'حبة واحدة']]], 'issued_snapshot' => ['doctor_name' => 'اسم الطبيب عند الإصدار', 'patient_name' => 'اسم المريض عند الإصدار']]);

        return [$patient, $doctor, $file, $visit, $prescription];
    }

    public function test_patient_timeline_excludes_drafts_and_pdf_uses_issued_snapshot(): void
    {
        [$patient, $doctor, $file, $visit, $prescription] = $this->patientDocument();
        DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id, 'document_version' => 2, 'corrects_prescription_id' => $prescription->id, 'correction_reason' => 'تصحيح قيد التوثيق', 'content' => ['notes' => 'مسودة سرية']]);
        $this->actingAs($patient->user, 'sanctum');
        $url = '/api/v1/patients/'.$patient->public_id.'/records';
        $this->getJson($url)->assertOk()->assertJsonCount(2, 'data')->assertDontSee('مسودة سرية');
        $this->getJson($url.'/prescription/'.$prescription->id)->assertOk()->assertJsonPath('data.doctor_name', 'اسم الطبيب عند الإصدار');
        $pdf = $this->get($url.'/prescription/'.$prescription->id.'/pdf')->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        if ($reviewPath = getenv('THERA_PDF_REVIEW_PATH')) {
            file_put_contents($reviewPath, $pdf->getContent());
        }
        $other = Patient::factory()->bookable()->create();
        $this->actingAs($other->user, 'sanctum')->getJson($url.'/prescription/'.$prescription->id.'/pdf')->assertNotFound();
    }

    public function test_doctor_reads_shared_record_only_with_live_consent(): void
    {
        [$patient, $doctor, $file] = $this->patientDocument();
        $url = route('doctor.patients.shared-record', $file);
        $this->actingAs($doctor, 'doctor')->get($url)->assertForbidden();
        $consent = PatientDoctorConsent::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'granted_by' => $patient->user_id]);
        $this->get($url)->assertOk()->assertSee('السجل المشارك')->assertSee('دواء تجريبي');
        $consent->update(['revoked_at' => now()]);
        $this->get($url)->assertForbidden();
        $consent->update(['revoked_at' => null, 'expires_at' => now()->subMinute()]);
        $this->get($url)->assertForbidden();
        $this->assertDatabaseHas('patient_access_events', ['doctor_id' => $doctor->id, 'action' => 'shared_medical_timeline_read']);
    }

    public function test_consent_granted_by_a_delegate_ends_when_delegation_is_revoked(): void
    {
        [$patient, $doctor, $file] = $this->patientDocument();
        $guardian = Patient::factory()->bookable()->create();
        $grant = PatientDelegation::factory()->create(['patient_id' => $patient->id, 'user_id' => $guardian->user_id, 'status' => 'approved', 'expires_at' => now()->addDays(30)]);
        PatientDoctorConsent::factory()->create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'granted_by' => $guardian->user_id]);
        $this->actingAs($doctor, 'doctor')->get(route('doctor.patients.shared-record', $file))->assertOk();
        $grant->update(['status' => 'revoked']);
        $this->get(route('doctor.patients.shared-record', $file))->assertForbidden();
    }
}
