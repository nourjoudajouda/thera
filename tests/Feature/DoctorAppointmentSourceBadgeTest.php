<?php

namespace Tests\Feature;

use App\Models\DoctorAppointment;
use App\Models\DoctorPatient;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorAppointmentSourceBadgeTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['list'])]
    #[TestWith(['week'])]
    #[TestWith(['details'])]
    public function test_patient_application_bookings_show_the_source_badge(string $view): void
    {
        $appointment = DoctorAppointment::factory()->create(['source' => DoctorAppointment::SOURCE_PATIENT_APP]);
        $this->actingAs($appointment->doctor, 'doctor');
        $this->get($this->url($appointment, $view))->assertOk()->assertSee('من تطبيق المريض')->assertSee('حجزه المريض من تطبيق THERA');
        $this->assertSame('patient_app', $appointment->fresh()->source);
    }

    #[TestWith(['list'])]
    #[TestWith(['week'])]
    #[TestWith(['details'])]
    public function test_a_thera_patient_booked_by_the_doctor_does_not_get_an_application_badge(string $view): void
    {
        $appointment = DoctorAppointment::factory()->create(['source' => 'doctor']);
        $patient = DoctorPatient::factory()->create(['doctor_id' => $appointment->doctor_id, 'patient_id' => Patient::factory()->bookable()->create()->id]);
        $appointment->update(['doctor_patient_id' => $patient->id]);
        $this->actingAs($appointment->doctor, 'doctor');
        $this->get($this->url($appointment, $view))->assertOk()->assertDontSee('من تطبيق المريض');
    }

    public function test_unknown_sources_are_not_mislabelled_and_foreign_app_bookings_are_not_disclosed(): void
    {
        $appointment = DoctorAppointment::factory()->create(['source' => 'facility']);
        $this->actingAs($appointment->doctor, 'doctor');
        $this->get($this->url($appointment, 'list'))->assertOk()->assertDontSee('من تطبيق المريض');
        $foreign = DoctorAppointment::factory()->create(['source' => DoctorAppointment::SOURCE_PATIENT_APP, 'patient_name' => 'مريض طبيب آخر']);
        $this->get($this->url($foreign, 'details'))->assertNotFound();
        $this->get($this->url($foreign, 'list'))->assertOk()->assertDontSee('مريض طبيب آخر')->assertDontSee('من تطبيق المريض');
        $this->assertDatabaseCount('doctor_appointments', 2);
    }

    private function url(DoctorAppointment $appointment, string $view): string
    {
        return $view === 'details'
            ? route('doctor.appointments.show', ['appointment' => $appointment->id])
            : route('doctor.appointments.index', ['view' => $view, 'date' => $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->toDateString()]);

    }
}
