<?php

namespace Tests\Feature;

use App\Models\DoctorAppointment;
use App\Models\DoctorPatient;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\PatientAccountCreated;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorTheraPatientRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_linked_account_with_hashed_password_and_one_encrypted_welcome_job_even_on_retry(): void
    {
        $service = $this->readyService();
        Queue::fake();
        $data = $this->payload($service);
        $data['patient_email'] = '  HIBA@EXAMPLE.TEST  ';

        $response = $this->postJson(route('doctor.appointments.store'), $data);
        $response->assertOk()->assertJsonPath('message', fn ($message) => str_contains($message, 'بانتظار الإرسال'));
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();

        $user = User::sole();
        $patient = Patient::sole();
        $appointment = DoctorAppointment::sole();
        $this->assertSame('hiba@example.test', $user->email);
        $this->assertSame($user->id, $patient->user_id);
        $this->assertSame($patient->id, $appointment->patient->patient_id);
        $this->assertSame($appointment->doctor_id, $appointment->patient->doctor_id);
        $this->assertNull($patient->phone_verified_at);
        $this->assertNull($user->email_verified_at);
        $this->assertFalse($patient->booking_enabled);
        $this->assertDatabaseCount('doctor_patients', 1);
        Queue::assertPushed(SendQueuedNotifications::class, function ($job) use ($user, $response, $patient): bool {
            $notification = $job->notification;
            $this->assertTrue($job->shouldBeEncrypted);
            $this->assertTrue($job->afterCommit);
            $this->assertSame('patient-onboarding', $job->queue);
            $this->assertTrue(Hash::check($notification->initialPassword, $user->password));
            $this->assertGreaterThanOrEqual(20, strlen($notification->initialPassword));
            $this->assertNotSame($notification->initialPassword, $user->password);
            $response->assertDontSee($notification->initialPassword);
            $message = $notification->toMail($user);
            $this->assertSame('smtp', $message->mailer);
            $this->assertStringContainsString('hiba@example.test', $message->render());
            $this->assertStringContainsString($patient->reference(), $message->render());

            return $notification instanceof PatientAccountCreated && $job->notifiables->sole()->is($user);
        });
        Queue::assertPushed(SendQueuedNotifications::class, 1);
    }

    public function test_existing_email_is_never_reused_or_reset_even_with_different_case(): void
    {
        $service = $this->readyService();
        $existing = User::factory()->create(['email' => 'HIBA@example.test']);
        $password = $existing->password;
        Queue::fake();

        $this->postJson(route('doctor.appointments.store'), $this->payload($service))
            ->assertUnprocessable()->assertJsonValidationErrors('patient_email');

        $this->assertSame($password, $existing->fresh()->password);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('patients', 0);
        $this->assertDatabaseCount('doctor_patients', 0);
        $this->assertDatabaseCount('doctor_appointments', 0);
        Queue::assertNothingPushed();
    }

    #[TestWith([''])]
    #[TestWith(['invalid-email'])]
    #[TestWith([['not-a-string']])]
    public function test_invalid_email_cannot_create_account_or_send_mail(mixed $email): void
    {
        $service = $this->readyService();
        Queue::fake();
        $data = $this->payload($service);
        $data['patient_email'] = $email;

        $this->postJson(route('doctor.appointments.store'), $data)->assertUnprocessable()->assertJsonValidationErrors('patient_email');

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('patients', 0);
        Queue::assertNothingPushed();
    }

    public function test_local_patient_creation_stays_local_and_ignores_network_fields(): void
    {
        $service = $this->readyService();
        Queue::fake();
        $data = $this->payload($service);
        $data['patient_mode'] = 'new';

        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();

        $this->assertDatabaseCount('doctor_patients', 1);
        $this->assertNull(DoctorPatient::sole()->patient_id);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('patients', 0);
        Queue::assertNothingPushed();
    }

    public function test_unavailable_time_cannot_leave_an_account_or_welcome_message(): void
    {
        $service = $this->readyService();
        Queue::fake();
        $data = $this->payload($service);
        $data['time'] = '03:00';

        $this->postJson(route('doctor.appointments.store'), $data)->assertUnprocessable();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('patients', 0);
        $this->assertDatabaseCount('doctor_patients', 0);
        Queue::assertNothingPushed();
    }

    public function test_unverified_doctor_cannot_create_network_accounts(): void
    {
        $service = $this->readyService();
        $service->doctorFacility->doctor->update(['verification_status' => 'draft']);
        Queue::fake();

        $this->postJson(route('doctor.appointments.store'), $this->payload($service))->assertForbidden();

        $this->assertDatabaseCount('users', 0);
        Queue::assertNothingPushed();
    }

    public function test_welcome_payload_is_encrypted_in_the_database_queue(): void
    {
        $service = $this->readyService();
        config(['queue.default' => 'database']);

        $this->postJson(route('doctor.appointments.store'), $this->payload($service))->assertOk();

        $job = DB::table('jobs')->sole();
        $payload = json_decode($job->payload, true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('patient-onboarding', $job->queue);
        $this->assertStringNotContainsString('hiba@example.test', $job->payload);
        $this->assertStringNotContainsString('initialPassword', $job->payload);
        $this->assertStringNotContainsString('O:', $payload['data']['command']);
        $this->assertDatabaseCount('failed_jobs', 0);
    }

    public function test_rollback_does_not_enqueue_welcome_for_an_uncommitted_account(): void
    {
        $service = $this->readyService();
        config(['queue.default' => 'database']);
        DB::beginTransaction();
        try {
            $this->postJson(route('doctor.appointments.store'), $this->payload($service))->assertOk();
            $this->assertDatabaseCount('jobs', 0);
        } finally {
            DB::rollBack();
        }

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('patients', 0);
        $this->assertDatabaseCount('doctor_appointments', 0);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_a_password_changed_before_delivery_prevents_sending_stale_credentials(): void
    {
        $user = User::factory()->create(['password' => 'initial-password']);
        $notification = new PatientAccountCreated('initial-password', 'TH-PRIVATE');
        $this->assertTrue($notification->shouldSend($user, 'mail'));
        $user->update(['password' => 'replacement-password']);

        $this->assertFalse($notification->shouldSend($user->fresh(), 'mail'));
    }

    private function readyService(): DoctorService
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-02 08:00', config('thera.schedule_timezone')));
        $service = DoctorService::factory()->create(['is_active' => true, 'duration_minutes' => 30]);
        $service->doctorFacility->facility->update(['is_active' => true, 'bookings_enabled' => true]);
        $service->doctorFacility->doctor->update(['verification_status' => 'verified']);
        DoctorSchedule::factory()->create(['doctor_facility_id' => $service->doctor_facility_id, 'is_active' => true, 'daily_capacity' => 20, 'periods' => [['weekday' => 1, 'start' => '09:00', 'end' => '12:00', 'breaks' => [], 'service_ids' => [$service->id]]]]);
        $this->actingAs($service->doctorFacility->doctor, 'doctor');

        return $service;
    }

    /** @return array<string, mixed> */
    private function payload(DoctorService $service): array
    {
        return ['doctor_service_id' => $service->id, 'date' => '2026-11-02', 'time' => '09:00', 'patient_mode' => 'new_thera', 'patient_name' => 'هبة عادل', 'patient_phone' => '+962790111222', 'patient_email' => 'hiba@example.test', 'request_key' => (string) Str::uuid()];
    }
}
