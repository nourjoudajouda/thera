<?php

namespace Tests\Feature;

use App\Models\DoctorAppointment;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorAppointmentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith([false, false])]
    #[TestWith([false, true])]
    #[TestWith([true, false])]
    #[TestWith([true, true])]
    public function test_completion_is_rejected_without_a_closed_clinical_visit(bool $creating, bool $arrived): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-16 10:00', config('thera.schedule_timezone')));
        $appointment = $creating ? DoctorAppointment::factory()->make() : DoctorAppointment::factory()->create();
        $appointment->fill(['status' => 'completed', 'arrived_at' => $arrived ? now() : null]);

        try {
            $appointment->save();
            $this->fail('Completion must require a closed clinical visit.');
        } catch (ValidationException $exception) {
            $this->assertSame($arrived
                ? 'إكمال الموعد يتطلب زيارة طبية مغلقة مرتبطة به.'
                : 'لا يمكن إكمال الموعد قبل تسجيل حضور المريض وإغلاق زيارته الطبية.', $exception->errors()['status'][0]);
        }

        if ($creating) {
            $this->assertDatabaseCount('doctor_appointments', 0);
        } else {
            $this->assertSame('confirmed', $appointment->fresh()->status);
            $this->assertNull($appointment->fresh()->arrived_at);
        }
        $this->assertDatabaseCount('doctor_appointment_events', 0);
    }

    public function test_arrival_and_elapsed_time_do_not_complete_an_appointment(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-16 10:00', config('thera.schedule_timezone')));
        $appointment = DoctorAppointment::factory()->create(['starts_at' => now()->subHour(), 'ends_at' => now()->subMinutes(30)]);

        $appointment->update(['arrived_at' => now()]);

        $this->assertSame('confirmed', $appointment->fresh()->status);
        $this->assertNotNull($appointment->fresh()->arrived_at);
    }

    public function test_the_status_endpoint_rejects_manual_completion_without_changing_the_appointment(): void
    {
        $appointment = DoctorAppointment::factory()->create();

        $this->actingAs($appointment->doctor, 'doctor')->patchJson(route('doctor.appointments.status', $appointment), ['action' => 'completed', 'revision' => 1])
            ->assertUnprocessable()->assertJsonValidationErrors('action');

        $this->assertSame('confirmed', $appointment->fresh()->status);
        $this->assertNull($appointment->fresh()->arrived_at);
        $this->assertDatabaseCount('doctor_appointment_events', 0);
    }

    public function test_legacy_completed_records_are_not_silently_given_an_arrival_or_reopened(): void
    {
        $appointment = DoctorAppointment::withoutEvents(fn () => DoctorAppointment::factory()->create(['status' => 'completed', 'arrived_at' => null]));

        $appointment->update(['revision' => 2]);

        $this->assertSame('completed', $appointment->fresh()->status);
        $this->assertNull($appointment->fresh()->arrived_at);
        $this->assertSame(2, $appointment->fresh()->revision);
    }
}
