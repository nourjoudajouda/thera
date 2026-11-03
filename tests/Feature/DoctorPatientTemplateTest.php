<?php

namespace Tests\Feature;

use App\Http\Controllers\Doctor\DoctorPatientController;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPatient;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorPatientTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_uses_the_template_without_inventing_missing_patient_data(): void
    {
        $patient = DoctorPatient::factory()->create();
        $response = $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.patients.show', ['patient' => $patient->id]));
        $response->assertOk()->assertViewHas('tab', 'summary')->assertViewHas('lastVisit', null)
            ->assertSee('patient-summary-grid')->assertSee('العمر غير مسجّل')->assertSee('غياب البيانات لا يعني عدم وجود حساسية.')
            ->assertSee('لوحة الطبيب')->assertDontSee('THERA معتمد');
        foreach (DoctorPatientController::TEMPLATE_TABS as $label) {
            $response->assertSee($label);
        }
    }

    public function test_template_tabs_select_only_the_linked_kind_and_do_not_treat_prescriptions_as_current_medications(): void
    {
        $patient = DoctorPatient::factory()->create();
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'doctor_patient_id' => $patient->id]);
        $visit = DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id, 'content' => ['diagnosis' => 'تشخيص موثق']]);
        $lab = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'lab', 'status' => 'approved', 'approved_at' => now()]);
        $imaging = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'radiology', 'status' => 'approved', 'approved_at' => now()]);
        $labReport = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $lab->id]);
        $imagingReport = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $imaging->id]);
        $followup = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'followup']);
        DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'referral']);
        DoctorExternalReport::factory()->create();
        $this->actingAs($patient->doctor, 'doctor');
        foreach (['diagnoses' => $visit->id, 'lab-results' => $labReport->id, 'imaging' => $imagingReport->id, 'followups' => $followup->id] as $tab => $id) {
            $response = $this->get(route('doctor.patients.show', ['patient' => $patient->id, 'tab' => $tab]));
            $response->assertOk()->assertViewHas('records', fn ($records) => $records->pluck('id')->all() === [$id]);
            if (in_array($tab, ['lab-results', 'imaging'], true)) {
                $response->assertSee('تقرير خارجي — غير معتمد من THERA');
            }
        }
        foreach (['procedures', 'medications', 'allergies'] as $tab) {
            $this->get(route('doctor.patients.show', ['patient' => $patient->id, 'tab' => $tab]))->assertOk()->assertViewHas('records', fn ($records) => $records->isEmpty());
        }
    }

    public function test_upcoming_checkbox_filters_confirmed_future_appointments_in_the_table(): void
    {
        $patient = DoctorPatient::factory()->create();
        DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'doctor_patient_id' => $patient->id, 'status' => 'confirmed', 'starts_at' => now()->addDay()]);
        $other = DoctorPatient::factory()->create(['doctor_id' => $patient->doctor_id]);
        DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'doctor_patient_id' => $other->id, 'status' => 'cancelled', 'starts_at' => now()->addDay()]);
        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.patients.index', ['tab' => 'files', 'upcoming' => 1]))
            ->assertOk()->assertSee('patient-desktop-table')->assertViewHas('records', fn ($records) => $records->pluck('id')->all() === [$patient->id]);
    }
}
