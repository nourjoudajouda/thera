<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorAppointmentEvent;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use App\Models\DoctorSpecialty;
use App\Models\Patient;
use App\Models\PatientFavorite;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PatientBookingApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    private function setupBooking(string $type = 'percentage'): array
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-01 08:00', 'Asia/Hebron'));
        config(['patient.development_payments' => true]);
        $patient = Patient::factory()->bookable()->create();
        $service = DoctorService::factory()->create(['is_active' => true]);
        $service->doctorFacility->facility->update(['is_active' => true, 'bookings_enabled' => true]);
        $doctor = $service->doctorFacility->doctor;
        $doctor->verification_status = 'verified';
        $doctor->account_preferences = ['payment_policy' => ['type' => $type, 'deposit_percent' => 20, 'remaining_timing' => 'before_visit', 'allow_early_payment' => true]];
        $doctor->save();
        DoctorSchedule::factory()->create(['doctor_facility_id' => $service->doctor_facility_id, 'periods' => [['weekday' => 0, 'start' => '09:00', 'end' => '12:00', 'breaks' => [], 'service_ids' => [$service->id]]]]);
        $this->actingAs($patient->user, 'sanctum');

        return [$patient, $service, '/api/v1/patients/'.$patient->public_id.'/appointments'];
    }

    private function payload($service): array
    {
        return ['doctor_service_id' => $service->id, 'date' => '2026-11-01', 'time' => '09:00', 'request_key' => (string) Str::uuid()];
    }

    public function test_booking_snapshot_payment_retry_and_cancellation_never_create_real_collection(): void
    {
        [$patient, $service, $url] = $this->setupBooking();
        $payload = $this->payload($service);
        $booking = $this->postJson($url, $payload)->assertOk()->assertJsonPath('data.status', 'pending_payment')->assertJsonPath('data.required_minor', 6000)->json('data');
        $this->postJson($url, $payload)->assertOk()->assertJsonPath('data.id', $booking['id']);
        $this->assertDatabaseCount('doctor_appointments', 1);
        $payment = ['request_key' => (string) Str::uuid(), 'outcome' => 'succeeded'];
        $this->postJson($url.'/'.$booking['id'].'/payments', $payment)->assertOk()->assertJsonPath('data.appointment.paid_minor', 6000)->assertJsonPath('data.appointment.status', 'confirmed');
        $this->postJson($url.'/'.$booking['id'].'/payments', $payment)->assertOk()->assertJsonPath('data.appointment.paid_minor', 6000);
        $this->assertDatabaseCount('patient_payment_attempts', 1);
        $this->assertDatabaseCount('doctor_payments', 0);
        $this->actingAs($service->doctorFacility->doctor, 'doctor')->get(route('doctor.finance.collections'))
            ->assertOk()->assertViewHas('totals', fn ($totals) => $totals->isEmpty())
            ->assertViewHas('appointments', fn ($appointments) => $appointments->isEmpty());
        $this->get(route('doctor.finance.collections', ['appointment' => $booking['id']]))->assertNotFound();
        $this->actingAs($patient->user, 'sanctum');
        $service->update(['price_minor' => 90000]);
        $this->getJson($url.'/'.$booking['id'])->assertOk()->assertJsonPath('data.price_minor', 30000);
        $this->patchJson($url.'/'.$booking['id'], ['action' => 'cancel', 'revision' => 2, 'reason' => 'تغيير الموعد'])->assertOk()->assertJsonPath('data.paid_minor', 0)->assertJsonPath('data.status', 'cancelled');
        $this->assertDatabaseHas('patient_payment_attempts', ['status' => 'reversed']);
    }

    public function test_none_full_expiration_and_patient_isolation(): void
    {
        [$patient, $service, $url] = $this->setupBooking('none');
        $booking = $this->postJson($url, $this->payload($service))->assertOk()->assertJsonPath('data.status', 'confirmed')->assertJsonPath('data.required_minor', 0)->json('data');
        $other = Patient::factory()->bookable()->create();
        $this->actingAs($other->user, 'sanctum')->getJson($url.'/'.$booking['id'])->assertNotFound();
        $this->actingAs($patient->user, 'sanctum');
        $this->postJson($url, $this->payload($service))->assertUnprocessable();
    }

    public function test_pending_payment_is_resolved_once_and_expired_hold_releases_capacity(): void
    {
        [$patient, $service, $url] = $this->setupBooking('full');
        $booking = $this->postJson($url, $this->payload($service))->assertOk()->assertJsonPath('data.required_minor', 30000)->json('data');
        $payUrl = $url.'/'.$booking['id'].'/payments';
        $this->postJson($payUrl, ['request_key' => (string) Str::uuid(), 'outcome' => 'pending'])->assertOk()->assertJsonPath('data.attempt.status', 'pending');
        $this->postJson($payUrl, ['request_key' => (string) Str::uuid(), 'outcome' => 'succeeded'])->assertStatus(409);
        $this->postJson($payUrl.'/resolve')->assertOk()->assertJsonPath('data.paid_minor', 30000);
        $this->postJson($payUrl.'/resolve')->assertOk()->assertJsonPath('data.paid_minor', 30000);
        $payload = $this->payload($service);
        $payload['time'] = '10:00';
        $hold = $this->postJson($url, $payload)->assertOk()->json('data');
        $this->travel(11)->minutes();
        $this->getJson($url.'/'.$hold['id'])->assertOk()->assertJsonPath('data.status', 'expired');
        $payload['request_key'] = (string) Str::uuid();
        $this->postJson($url, $payload)->assertOk();
        $this->postJson($url.'/'.$hold['id'].'/payments', ['request_key' => (string) Str::uuid(), 'outcome' => 'succeeded'])->assertStatus(409);
    }

    public function test_simulation_is_unavailable_in_production_and_search_hides_inactive_services(): void
    {
        [$patient, $service, $url] = $this->setupBooking();
        $this->getJson('/api/v1/doctors')->assertOk()->assertJsonCount(1, 'data');
        $assignment = DoctorSpecialty::factory()->verified()->create(['doctor_id' => $service->doctorFacility->doctor_id]);
        $this->getJson('/api/v1/doctor-filters')->assertOk()->assertJsonPath('data.specialties.0.id', $assignment->specialty_id)->assertJsonPath('data.cities.0', $service->doctorFacility->facility->city);
        $this->getJson('/api/v1/doctors?favorite_only=1')->assertOk()->assertJsonCount(0, 'data');
        PatientFavorite::create(['user_id' => $patient->user_id, 'doctor_id' => $service->doctorFacility->doctor_id]);
        $this->getJson('/api/v1/doctors?favorite_only=1&specialty_id='.$assignment->specialty_id)->assertOk()->assertJsonCount(1, 'data');
        $this->app->instance('env', 'production');
        $this->postJson($url, $this->payload($service))->assertStatus(503);
        $this->assertDatabaseCount('doctor_appointments', 0);
        $service->update(['is_active' => false]);
        $this->getJson('/api/v1/doctors')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_check_in_is_private_idempotent_and_queue_exposes_only_a_count(): void
    {
        [$patient, $service, $url] = $this->setupBooking('full');
        $booking = $this->postJson($url, $this->payload($service))->assertOk()->json('data');
        $base = $url.'/'.$booking['id'];
        $this->postJson($base.'/payments', ['request_key' => (string) Str::uuid(), 'outcome' => 'succeeded'])->assertOk();
        $this->postJson($base.'/check-in', ['code' => 'THERA:invalid'])->assertUnprocessable();
        $code = $service->doctorFacility->facility->checkInCode();
        $this->postJson($base.'/check-in', ['code' => $code])->assertOk()->assertJsonPath('data.queue_state', 'ready')->assertJsonPath('data.ahead', 0);
        $this->postJson($base.'/check-in', ['code' => $code])->assertOk()->assertJsonPath('data.revision', 3);
        $this->assertSame(1, DoctorAppointmentEvent::where('action', 'arrived')->count());
        $other = $this->payload($service);
        $other['time'] = '09:30';
        $second = $this->postJson($url, $other)->assertOk()->json('data.id');
        $this->postJson($url.'/'.$second.'/payments', ['request_key' => (string) Str::uuid(), 'outcome' => 'succeeded'])->assertOk();
        $this->postJson($url.'/'.$second.'/check-in', ['code' => $code])->assertOk()->assertJsonPath('data.ahead', 1)->assertJsonMissingPath('data.patient_phone');
        $this->travel(1)->days();
        $this->postJson($base.'/check-in', ['code' => $code])->assertStatus(409);
        $this->actingAs($service->doctorFacility->doctor, 'doctor')->get(route('doctor.facilities.check-in', $service->doctorFacility->facility))->assertOk()->assertSee('<svg', false);
        $this->actingAs(Doctor::factory()->create(), 'doctor')->get(route('doctor.facilities.check-in', $service->doctorFacility->facility))->assertNotFound();
    }
}
