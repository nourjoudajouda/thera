<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPayment;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_are_doctor_authenticated_and_do_not_accept_admin_sessions(): void
    {
        $this->getJson(route('doctor.shell.notifications'))->assertUnauthorized();
        $this->actingAs(Admin::factory()->create(), 'admin')->getJson(route('doctor.shell.notifications'))->assertUnauthorized();
    }

    public function test_notifications_are_scoped_to_doctor_preferences_and_local_day_without_patient_details(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-02 10:00', 'Asia/Hebron'));
        $doctor = Doctor::factory()->create();
        $this->enableNotifications($doctor);
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'starts_at' => CarbonImmutable::parse('2026-11-02 00:15', 'Asia/Hebron')->utc(), 'arrived_at' => now(), 'patient_name' => 'بيانات خاصة لا تظهر', 'price_minor' => 30000]);
        DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'starts_at' => CarbonImmutable::parse('2026-11-03 00:15', 'Asia/Hebron')->utc()]);
        DoctorAppointment::factory()->create(['starts_at' => $appointment->starts_at, 'arrived_at' => now()]);
        DoctorExternalReport::factory()->create(['doctor_id' => $doctor->id, 'doctor_diagnostic_order_id' => DoctorDiagnosticOrder::factory()->create(['doctor_id' => $doctor->id, 'status' => 'approved', 'approved_at' => now()])->id]);
        DoctorExternalReport::factory()->create();
        DoctorReferral::factory()->create(['doctor_id' => $doctor->id, 'doctor_visit_id' => DoctorVisit::factory()->create(['doctor_id' => $doctor->id])->id]);
        DoctorReferral::factory()->create();

        $this->actingAs($doctor, 'doctor')->getJson(route('doctor.shell.notifications'))->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private')->assertJsonPath('total', 5)
            ->assertJsonPath('items.0.count', 1)->assertJsonPath('items.1.count', 1)
            ->assertJsonPath('items.2.key', 'results')->assertJsonPath('items.3.key', 'referrals')->assertJsonPath('items.4.key', 'payments')
            ->assertDontSee('بيانات خاصة لا تظهر')->assertDontSee($appointment->patient_phone);
    }

    public function test_disabled_preferences_hide_alerts_and_paid_appointments_have_no_collection_alert(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-02 10:00', 'Asia/Hebron'));
        $appointment = DoctorAppointment::factory()->create(['starts_at' => now(), 'arrived_at' => now(), 'price_minor' => 30000]);
        $doctor = $appointment->doctor;
        $this->actingAs($doctor, 'doctor')->getJson(route('doctor.shell.notifications'))->assertOk()->assertExactJson(['items' => [], 'total' => 0]);
        $preferences = $doctor->accountPreferences();
        $preferences['notifications']['payments'] = true;
        $doctor->account_preferences = $preferences;
        $doctor->save();
        DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id, 'doctor_id' => $doctor->id, 'amount_minor' => 30000]);
        $this->actingAs($doctor->fresh(), 'doctor')->getJson(route('doctor.shell.notifications'))->assertOk()->assertExactJson(['items' => [], 'total' => 0]);
    }

    #[TestWith(['doctor.dashboard'])]
    #[TestWith(['doctor.appointments.index'])]
    #[TestWith(['doctor.appointments.create'])]
    #[TestWith(['doctor.patients.index'])]
    #[TestWith(['doctor.clinical.visits'])]
    #[TestWith(['doctor.clinical.prescriptions'])]
    #[TestWith(['doctor.orders.lab'])]
    #[TestWith(['doctor.orders.radiology'])]
    #[TestWith(['doctor.orders.results'])]
    #[TestWith(['doctor.clinical.referrals'])]
    #[TestWith(['doctor.practice.services'])]
    #[TestWith(['doctor.practice.schedule'])]
    #[TestWith(['doctor.finance.collections'])]
    #[TestWith(['doctor.account.settings'])]
    #[TestWith(['doctor.account.professional-profile'])]
    #[TestWith(['doctor.account.verification'])]
    public function test_main_workspace_pages_render_one_shared_accessible_account_menu_and_topbar(string $route): void
    {
        $doctor = Doctor::factory()->create(['verification_status' => 'verified', 'name' => 'د. هبة عادل']);
        $response = $this->actingAs($doctor, 'doctor')->get(route($route))->assertOk()->assertSee('د. هبة عادل')
            ->assertSee('data-doctor-destination="profile"', false)->assertSee('doctor-theme-toggle')->assertSee('doctor-language-toggle')->assertSee('doctor-notifications-toggle')->assertSee('doctor-shell.js');
        $this->assertSame(1, substr_count($response->getContent(), 'id="doctor-card-trigger"'));
        $this->assertSame(1, substr_count($response->getContent(), 'id="doctor-account-menu"'));
        $this->assertSame(1, substr_count($response->getContent(), '/js/doctor-workspace.js'));
    }

    private function enableNotifications(Doctor $doctor): void
    {
        $preferences = $doctor->accountPreferences();
        $preferences['notifications'] = array_fill_keys(array_keys(Doctor::NOTIFICATION_PREFERENCES), true);
        $doctor->account_preferences = $preferences;
        $doctor->save();

    }
}
