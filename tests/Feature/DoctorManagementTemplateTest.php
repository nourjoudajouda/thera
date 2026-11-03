<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorReferral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorManagementTemplateTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['doctor.orders.results', 'النتيجة / الطلب'])]
    #[TestWith(['doctor.clinical.referrals', 'الجهة / موعد المتابعة'])]
    #[TestWith(['doctor.practice.services', 'مكان تقديم الخدمة'])]
    #[TestWith(['doctor.practice.schedule', 'بداية الدوام'])]
    #[TestWith(['doctor.finance.collections', 'محصل في المنشأة'])]
    public function test_template_keeps_table_headings_and_existing_controls_when_empty(string $route, string $heading): void
    {
        $doctor = Doctor::factory()->create(['verification_status' => 'verified']);

        $this->actingAs($doctor, 'doctor')->get(route($route))
            ->assertOk()
            ->assertSee('management-template', false)
            ->assertSee('doctor-management-template.css')
            ->assertSee('<table', false)
            ->assertSee($heading)
            ->assertDontSee('>بحث</button>', false)
            ->assertDontSee('>تطبيق</button>', false);
    }

    public function test_referral_tabs_keep_doctor_scoped_counts_and_filter_real_documents(): void
    {
        $referral = DoctorReferral::factory()->create(['content' => ['title' => 'إحالة ظاهرة']]);
        $followup = DoctorReferral::factory()->create(['doctor_id' => $referral->doctor_id, 'doctor_visit_id' => $referral->doctor_visit_id, 'kind' => 'followup', 'content' => ['title' => 'متابعة ظاهرة']]);
        DoctorReferral::factory()->create(['content' => ['title' => 'مستند طبيب آخر']]);

        $this->actingAs($referral->doctor, 'doctor')->get(route('doctor.clinical.referrals', ['kind' => 'followup']))
            ->assertOk()
            ->assertViewHas('kindCounts', ['referral' => 1, 'followup' => 1])
            ->assertViewHas('counts', ['total' => 2, 'draft' => 2, 'approved' => 0])
            ->assertViewHas('documents', fn ($documents) => $documents->modelKeys() === [$followup->id])
            ->assertSee('متابعة ظاهرة')
            ->assertDontSee('إحالة ظاهرة')
            ->assertDontSee('مستند طبيب آخر')
            ->assertSee(route('doctor.clinical.referrals.show', $followup))
            ->assertSee('document-mobile-list', false);
    }

    #[TestWith(['newest', false])]
    #[TestWith(['oldest', true])]
    #[TestWith(['urgent', true])]
    public function test_referral_sorting_preserves_owned_records(string $sort, bool $firstComesFirst): void
    {
        $first = DoctorReferral::factory()->create(['priority' => 'urgent']);
        $second = DoctorReferral::factory()->create(['doctor_id' => $first->doctor_id, 'doctor_visit_id' => $first->doctor_visit_id]);
        DoctorReferral::factory()->create(['priority' => 'urgent']);
        $expected = $firstComesFirst ? [$first->id, $second->id] : [$second->id, $first->id];

        $this->actingAs($first->doctor, 'doctor')->get(route('doctor.clinical.referrals', ['sort' => $sort]))
            ->assertOk()->assertViewHas('documents', fn ($documents) => $documents->modelKeys() === $expected);
    }

    #[TestWith(['newest', false])]
    #[TestWith(['oldest', true])]
    #[TestWith(['pending', true])]
    public function test_result_sorting_preserves_owned_reports(string $sort, bool $firstComesFirst): void
    {
        $order = DoctorDiagnosticOrder::factory()->create(['status' => 'approved', 'approved_at' => now()]);
        $first = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id]);
        $second = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id, 'reviewed_at' => now()]);
        DoctorExternalReport::factory()->create();
        $expected = $firstComesFirst ? [$first->id, $second->id] : [$second->id, $first->id];

        $this->actingAs($order->doctor, 'doctor')->get(route('doctor.orders.results', ['sort' => $sort]))
            ->assertOk()->assertViewHas('reports', fn ($reports) => $reports->modelKeys() === $expected);
    }

    #[TestWith(['doctor.orders.results'])]
    #[TestWith(['doctor.clinical.referrals'])]
    public function test_unknown_sort_values_are_rejected(string $route): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')->getJson(route($route, ['sort' => 'unknown']))
            ->assertUnprocessable()->assertJsonValidationErrors('sort');
    }
}
