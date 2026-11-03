<?php

namespace Tests\Feature;

use App\Models\DoctorAppointment;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorAppointmentVisitBadgeTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['list', 0])]
    #[TestWith(['week', 0])]
    #[TestWith(['list', -1])]
    public function test_open_visit_waiting_for_results_has_waiting_badge_and_local_arrival_time(string $view, int $dayOffset): void
    {
        $appointment = $this->appointment('pending_results', $dayOffset);

        $this->actingAs($appointment->doctor, 'doctor')->get($this->url($appointment, $view))
            ->assertOk()->assertSee('بانتظار الفحوصات والتحاليل')->assertDontSee('داخل الزيارة')
            ->assertSee('وقت الوصول:')->assertSee('08:17');

        $this->assertSame('pending_results', $appointment->visit->fresh()->status);
        $this->assertNull($appointment->visit->closed_at);
    }

    #[TestWith(['draft'])]
    #[TestWith(['initial'])]
    public function test_other_open_visit_states_still_show_inside_visit(string $status): void
    {
        $appointment = $this->appointment($status);

        $this->actingAs($appointment->doctor, 'doctor')->get($this->url($appointment))
            ->assertOk()->assertSee('داخل الزيارة')->assertDontSee('بانتظار الفحوصات والتحاليل')
            ->assertSee('وقت الوصول:')->assertSee('08:17');
    }

    public function test_closed_visit_does_not_show_waiting_or_inside_visit_and_keeps_arrival_time(): void
    {
        $appointment = $this->appointment('initial');
        $appointment->visit->update(['status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم موثق']]);
        $appointment->update(['status' => 'completed']);

        $this->actingAs($appointment->doctor, 'doctor')->get($this->url($appointment))
            ->assertOk()->assertDontSee('بانتظار الفحوصات والتحاليل')->assertDontSee('داخل الزيارة')
            ->assertSee('وقت الوصول:')->assertSee('08:17');
    }

    public function test_appointment_without_arrival_has_no_invented_arrival_time(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-02 10:15', 'Asia/Hebron'));
        $appointment = DoctorAppointment::factory()->create();

        $this->actingAs($appointment->doctor, 'doctor')->get($this->url($appointment))
            ->assertOk()->assertSee('لم يسجل حضور')->assertDontSee('وقت الوصول:');
    }

    private function appointment(string $status, int $dayOffset = 0): DoctorAppointment
    {
        config(['thera.schedule_timezone' => 'Asia/Hebron']);
        $this->travelTo(CarbonImmutable::parse('2026-11-02 10:15', 'Asia/Hebron'));
        $localDay = CarbonImmutable::parse('2026-11-02', 'Asia/Hebron')->addDays($dayOffset);
        $appointment = DoctorAppointment::factory()->create([
            'starts_at' => $localDay->setTime(9, 0)->utc(),
            'ends_at' => $localDay->setTime(9, 30)->utc(),
            'arrived_at' => $localDay->setTime(8, 17)->utc(),
        ]);
        DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id, 'status' => $status]);

        return $appointment->load('visit');
    }

    private function url(DoctorAppointment $appointment, string $view = 'list'): string
    {
        return route('doctor.appointments.index', ['view' => $view, 'date' => $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->toDateString()]);
    }
}
