<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPatient;
use App\Models\DoctorPrescription;
use App\Models\DoctorReferral;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DoctorPatientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_identical_contacts_remain_unlinked_and_are_not_automatically_merged(): void
    {
        $first = DoctorAppointment::factory()->create(['patient_name' => 'هبة عادل']);
        $second = DoctorAppointment::factory()->create(['doctor_facility_id' => $first->doctor_facility_id, 'patient_name' => 'هبة عادل', 'patient_phone' => $first->patient_phone]);
        $this->actingAs($first->doctor, 'doctor')->get(route('doctor.patients.index'))->assertOk()->assertViewHas('tab', 'files')->assertViewHas('records', fn ($items) => $items->isEmpty());
        $this->assertDatabaseCount('doctor_patients', 0);
        $this->getJson(route('doctor.appointments.contacts', ['q' => 'هبة']))->assertOk()->assertExactJson(['results' => []]);
        $this->assertNull($second->fresh()->doctor_patient_id);
    }

    public function test_guest_admin_and_other_doctor_are_denied_and_lookup_is_doctor_scoped(): void
    {
        $patient = DoctorPatient::factory()->create();
        $url = route('doctor.patients.show', ['patient' => $patient->id]);
        $this->getJson($url)->assertUnauthorized();
        $this->actingAs(Admin::factory()->create(), 'admin')->getJson($url)->assertUnauthorized();
        $other = Doctor::factory()->create();
        $this->actingAs($other, 'doctor')->get($url)->assertNotFound();
        $this->getJson(route('doctor.appointments.contacts', ['q' => $patient->name]))->assertOk()->assertExactJson(['results' => []]);
        $this->get(route('doctor.appointments.create', ['patient' => $patient->id]))->assertNotFound();
        $this->assertFalse(Gate::forUser($other)->inspect('view', $patient)->allowed());
        $this->assertTrue(Gate::forUser($patient->doctor)->inspect('view', $patient)->allowed());
        $this->assertFalse(Gate::forUser($patient->doctor)->allows('delete', $patient));
    }

    public function test_every_profile_tab_is_limited_to_linked_records_despite_identical_contact_details(): void
    {
        $patient = DoctorPatient::factory()->create(['name' => 'هبة عادل']);
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'doctor_patient_id' => $patient->id, 'patient_name' => $patient->name, 'patient_phone' => $patient->phone]);
        $visit = DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id]);
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => ['patient_name' => $patient->name]]);
        $report = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id]);
        $referral = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $unlinked = DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'patient_name' => $patient->name, 'patient_phone' => $patient->phone]);
        $foreign = DoctorAppointment::factory()->create(['patient_name' => $patient->name, 'patient_phone' => $patient->phone]);
        DoctorVisit::factory()->create(['doctor_appointment_id' => $foreign->id]);
        $this->actingAs($patient->doctor, 'doctor');
        foreach (['timeline' => $appointment->id, 'appointments' => $appointment->id, 'visits' => $visit->id, 'prescriptions' => $prescription->id, 'orders' => $order->id, 'reports' => $report->id, 'referrals' => $referral->id] as $tab => $id) {
            $this->get(route('doctor.patients.show', ['patient' => $patient->id, 'tab' => $tab]))->assertOk()->assertViewHas('records', fn ($records) => $records->pluck('id')->all() === [$id]);
        }
        $this->assertSame(7, $patient->events()->where('action', 'viewed')->count());
        $this->assertNull($unlinked->fresh()->doctor_patient_id);
    }

    public function test_directory_filters_search_name_phone_id_and_presence_of_visits(): void
    {
        $patient = DoctorPatient::factory()->create(['name' => 'مريم سليم', 'phone' => '+962790111222']);
        DoctorPatient::factory()->create(['doctor_id' => $patient->doctor_id, 'name' => 'شخص آخر']);
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'doctor_patient_id' => $patient->id]);
        DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id]);
        $this->actingAs($patient->doctor, 'doctor');
        foreach (['مريم', '790111222', (string) $patient->id, $patient->reference()] as $search) {
            $this->get(route('doctor.patients.index', ['tab' => 'files', 'search' => $search, 'status' => 'with_visits']))->assertOk()->assertViewHas('records', fn ($records) => $records->pluck('id')->all() === [$patient->id]);
        }
    }

    public function test_new_booking_creates_one_file_on_retry_and_never_merges_same_name_or_phone(): void
    {
        $service = $this->readyService();
        $data = $this->bookingPayload($service);
        Mail::fake();
        Notification::fake();
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();
        $this->assertDatabaseCount('doctor_patients', 1);
        $data['request_key'] = (string) Str::uuid();
        $data['time'] = '10:30';
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();
        $this->assertDatabaseCount('doctor_patients', 2);
        $this->assertSame(2, DoctorAppointment::distinct()->count('doctor_patient_id'));
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_existing_patient_booking_uses_server_identity_and_preserves_file_id(): void
    {
        $service = $this->readyService();
        $patient = DoctorPatient::factory()->create(['doctor_id' => $service->doctorFacility->doctor_id, 'name' => 'اسم الملف الصحيح']);
        $data = $this->bookingPayload($service) + ['doctor_patient_id' => $patient->id];
        $data['patient_mode'] = 'existing';
        $data['patient_name'] = 'اسم مزور';
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();
        $appointment = DoctorAppointment::sole();
        $this->assertSame($patient->id, $appointment->doctor_patient_id);
        $this->assertSame('اسم الملف الصحيح', $appointment->patient_name);
        $this->assertSame($patient->phone, $appointment->patient_phone);
        $this->assertDatabaseCount('doctor_patients', 1);
        $this->get(route('doctor.appointments.create', ['patient' => $patient->id]))->assertOk()->assertSee('PT-'.$patient->id);
    }

    public function test_failed_booking_does_not_leave_an_orphan_patient_and_foreign_ids_are_rejected(): void
    {
        $service = $this->readyService();
        $data = $this->bookingPayload($service);
        $data['time'] = '03:00';
        $this->postJson(route('doctor.appointments.store'), $data)->assertUnprocessable();
        $this->assertDatabaseCount('doctor_patients', 0);
        $foreign = DoctorPatient::factory()->create();
        $data = $this->bookingPayload($service);
        $data['patient_mode'] = 'existing';
        $data['doctor_patient_id'] = $foreign->id;
        $this->postJson(route('doctor.appointments.store'), $data)->assertUnprocessable()->assertJsonValidationErrors('doctor_patient_id');
        $this->assertDatabaseCount('doctor_appointments', 0);
    }

    public function test_model_rejects_reassigning_an_existing_patient_link(): void
    {
        $patient = DoctorPatient::factory()->create();
        $appointment = DoctorAppointment::factory()->create(['doctor_id' => $patient->doctor_id, 'doctor_patient_id' => $patient->id]);
        $other = DoctorPatient::factory()->create(['doctor_id' => $patient->doctor_id]);
        $this->expectException(ValidationException::class);
        $appointment->update(['doctor_patient_id' => $other->id]);
    }

    public function test_names_are_html_escaped_and_unknown_tabs_are_rejected(): void
    {
        $patient = DoctorPatient::factory()->create(['name' => '<script>alert(1)</script>']);
        $this->actingAs($patient->doctor, 'doctor')->get(route('doctor.patients.show', ['patient' => $patient->id]))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get(route('doctor.patients.show', ['patient' => $patient->id, 'tab' => 'invalid']))->assertRedirect();
    }

    public function test_identity_confirmation_endpoints_are_removed_without_changing_existing_records(): void
    {
        $appointment = DoctorAppointment::factory()->create();
        $this->actingAs($appointment->doctor, 'doctor');
        $this->get('/doctor/patients/link?appointment='.$appointment->id)->assertNotFound();
        $this->postJson('/doctor/patients/appointments/'.$appointment->id.'/link', ['patient_mode' => 'new', 'identity_confirmed' => 1])->assertNotFound();
        $this->get(route('doctor.appointments.show', ['appointment' => $appointment->id]))->assertOk()->assertDontSee('تأكيد الهوية');
        $this->assertNull($appointment->fresh()->doctor_patient_id);
        $this->assertDatabaseCount('doctor_patients', 0);
    }

    public function test_patient_directory_keeps_template_scopes_without_exposing_unlinked_appointments(): void
    {
        $patient = DoctorPatient::factory()->create();
        DoctorPatient::factory()->create();
        $this->actingAs($patient->doctor, 'doctor');
        $this->get(route('doctor.patients.index'))->assertOk()->assertSee('وصول مصرح حاليًا')->assertDontSee('تأكيد هوية')->assertDontSee('تأكيد الهوية')
            ->assertViewHas('records', fn ($records) => $records->pluck('id')->all() === [$patient->id]);
        $this->get(route('doctor.patients.index', ['tab' => 'authorized']))->assertOk()->assertViewHas('records', fn ($records) => $records->isEmpty());
        $this->get(route('doctor.patients.index', ['access' => 'authorized']))->assertOk()->assertViewHas('records', fn ($records) => $records->pluck('id')->all() === [$patient->id]);
        foreach (['limited', 'expired', 'denied'] as $access) {
            $this->get(route('doctor.patients.index', ['access' => $access]))->assertOk()->assertViewHas('records', fn ($records) => $records->isEmpty());
        }
        $this->get(route('doctor.patients.index', ['tab' => 'unlinked']))->assertRedirectToRoute('doctor.patients.index');
    }

    private function readyService(): DoctorService
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-31 08:00', config('thera.schedule_timezone')));
        $service = DoctorService::factory()->create(['is_active' => true, 'duration_minutes' => 30]);
        $service->doctorFacility->facility->update(['is_active' => true, 'bookings_enabled' => true]);
        $service->doctorFacility->doctor->update(['verification_status' => 'verified']);
        DoctorSchedule::factory()->create(['doctor_facility_id' => $service->doctor_facility_id, 'is_active' => true, 'daily_capacity' => 20, 'periods' => [['weekday' => 6, 'start' => '09:00', 'end' => '12:00', 'breaks' => [], 'service_ids' => [$service->id]]]]);
        $this->actingAs($service->doctorFacility->doctor, 'doctor');

        return $service;
    }

    /** @return array<string,mixed> */
    private function bookingPayload(DoctorService $service): array
    {
        return ['doctor_service_id' => $service->id, 'date' => '2026-10-31', 'time' => '09:00', 'patient_mode' => 'new', 'patient_name' => 'ليلى محمود', 'patient_phone' => '+962790111222', 'request_key' => (string) Str::uuid()];
    }
}
