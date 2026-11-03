<?php

namespace Tests\Feature;

use App\Models\DoctorAppointment;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorVisitTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_original_visits_table_and_counters_use_only_the_current_doctors_records(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-01 10:00', config('thera.schedule_timezone')));
        $visit = DoctorVisit::factory()->create(['content' => ['complaint' => 'صداع مستمر']]);
        DoctorVisit::factory()->create(['doctor_id' => $visit->doctor_id, 'doctor_facility_id' => $visit->doctor_facility_id, 'doctor_appointment_id' => DoctorAppointment::factory()->create(['doctor_id' => $visit->doctor_id, 'doctor_facility_id' => $visit->doctor_facility_id])->id, 'started_at' => now()->subDay(), 'status' => 'pending_results']);
        DoctorVisit::factory()->create();
        $this->actingAs($visit->doctor, 'doctor')->get(route('doctor.clinical.visits'))->assertOk()
            ->assertSee('<table', false)->assertSee('الشكوى الرئيسية')->assertSee('آخر تحديث')->assertSee('صداع مستمر')
            ->assertSee('تاريخ الزيارة')->assertSee('ترتيب الزيارات')->assertViewHas('counts', ['all' => 2, 'active' => 2, 'completed' => 0, 'today' => 1, 'completed_today' => 0]);
    }

    public function test_tabs_date_search_and_sort_filter_real_visits_without_changing_clinical_records(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-01 10:00', config('thera.schedule_timezone')));
        $appointment = DoctorAppointment::factory()->create(['patient_name' => 'سارة محمود', 'arrived_at' => now()]);
        $active = DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id, 'updated_at' => now()->subMinutes(10)]);
        $oldAppointment = DoctorAppointment::factory()->create(['doctor_id' => $appointment->doctor_id, 'doctor_facility_id' => $appointment->doctor_facility_id, 'patient_name' => 'خالد أحمد', 'arrived_at' => now()->subDay()]);
        $completed = DoctorVisit::factory()->create(['doctor_appointment_id' => $oldAppointment->id, 'status' => 'completed', 'closed_at' => now()->subDay(), 'started_at' => now()->subDay(), 'content' => ['assessment' => 'تقييم موثق'], 'updated_at' => now()->subDay()]);
        $this->actingAs($appointment->doctor, 'doctor');
        $url = route('doctor.clinical.visits');
        $this->get($url.'?tab=active')->assertOk()->assertViewHas('visits', fn ($rows) => $rows->modelKeys() === [$active->id]);
        $this->get($url.'?tab=completed')->assertOk()->assertViewHas('visits', fn ($rows) => $rows->modelKeys() === [$completed->id]);
        $this->get($url.'?date=2026-11-01')->assertOk()->assertViewHas('visits', fn ($rows) => $rows->modelKeys() === [$active->id]);
        $this->get($url.'?search='.urlencode('سارة'))->assertOk()->assertViewHas('visits', fn ($rows) => $rows->modelKeys() === [$active->id]);
        $this->get($url.'?sort=oldest')->assertOk()->assertViewHas('visits', fn ($rows) => $rows->modelKeys() === [$completed->id, $active->id]);
        $this->get($url)->assertOk()->assertViewHas('visits', fn ($rows) => $rows->modelKeys() === [$active->id, $completed->id]);
        $this->assertSame('draft', $active->fresh()->status);
        $this->assertNotNull($completed->fresh()->closed_at);
        $this->assertDatabaseEmpty('doctor_visit_revisions');
    }

    #[TestWith(['tab', 'invalid'])]
    #[TestWith(['date', 'not-a-date'])]
    #[TestWith(['sort', 'unexpected'])]
    public function test_template_filter_values_are_validated(string $key, string $value): void
    {
        $visit = DoctorVisit::factory()->create();
        $this->actingAs($visit->doctor, 'doctor')->getJson(route('doctor.clinical.visits', [$key => $value]))->assertUnprocessable()->assertJsonValidationErrors($key);
    }

    public function test_template_preserves_fields_routes_and_read_only_clinical_data_without_local_storage(): void
    {
        $visit = DoctorVisit::factory()->create(['content' => ['clinical_notes' => '<script>unsafe()</script>', 'diagnosis' => 'تشخيص أول\nتشخيص ثان', 'vitals' => ['pulse' => 76]]]);
        $page = $this->actingAs($visit->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk();
        $page->assertSee('template-clinical-grid', false)->assertSee('template-action-grid', false)->assertSee('السياق الطبي المتاح')->assertSee('visit-vitals-modal', false)
            ->assertSee('form="visit-documentation-form"', false)->assertSee('name="vitals[pulse]"', false)->assertSee('data-system-visit-status', false)->assertDontSee('name="status"', false)
            ->assertSee('<script>unsafe()</script>')->assertDontSee('<script>unsafe()</script>', false)->assertDontSee('localStorage', false);
        $this->assertSame(76, $visit->fresh()->content['vitals']['pulse']);
        $this->assertDatabaseCount('doctor_visit_revisions', 1);

    }
}
