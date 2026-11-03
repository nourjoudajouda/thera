<?php

namespace Tests\Feature;

use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPrescription;
use App\Models\DoctorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorDocumentDirectoryTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_prescription_template_filters_real_owned_records_by_status_search_and_sort(): void
    {
        $first = DoctorPrescription::factory()->create();
        $visit = DoctorVisit::factory()->create(['doctor_id' => $first->doctor_id]);
        $second = DoctorPrescription::factory()->create(['doctor_id' => $first->doctor_id, 'doctor_visit_id' => $visit->id, 'status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => ['patient_name' => 'خالد أحمد']]);
        DoctorPrescription::factory()->create();
        $url = route('doctor.clinical.prescriptions');
        $this->actingAs($first->doctor, 'doctor')->get($url)->assertOk()->assertSee('<table', false)->assertSee('حالة الصرف')->assertSee('document-mobile-list', false)
            ->assertViewHas('counts', ['total' => 2, 'draft' => 1, 'approved' => 1])->assertViewHas('prescriptions', fn ($rows) => $rows->modelKeys() === [$second->id, $first->id]);
        $this->get($url.'?status=draft')->assertOk()->assertViewHas('prescriptions', fn ($rows) => $rows->modelKeys() === [$first->id]);
        $this->get($url.'?status=approved&search='.urlencode('خالد'))->assertOk()->assertViewHas('prescriptions', fn ($rows) => $rows->modelKeys() === [$second->id]);
        $this->get($url.'?sort=oldest')->assertOk()->assertViewHas('prescriptions', fn ($rows) => $rows->modelKeys() === [$first->id, $second->id]);
        $this->getJson($url.'?status=sent')->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->getJson($url.'?sort=invalid')->assertUnprocessable()->assertJsonValidationErrors('sort');
    }

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    public function test_order_template_shows_external_report_and_review_counts_without_claiming_thera_results(string $kind): void
    {
        $order = DoctorDiagnosticOrder::factory()->create(['kind' => $kind, 'status' => 'approved', 'approved_at' => now()]);
        $report = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id]);
        $empty = DoctorDiagnosticOrder::factory()->create(['doctor_id' => $order->doctor_id, 'doctor_visit_id' => $order->doctor_visit_id, 'kind' => $kind]);
        DoctorDiagnosticOrder::factory()->create(['kind' => $kind]);
        $url = route('doctor.orders.'.$kind);
        $this->actingAs($order->doctor, 'doctor')->get($url)->assertOk()->assertSee('<table', false)->assertSee('مراجعة الطبيب')->assertSee('تقرير خارجي مرفوع')->assertSee('بانتظار المراجعة')
            ->assertViewHas('counts', ['total' => 2, 'draft' => 1, 'approved' => 1]);
        $this->get($url.'?result=external')->assertOk()->assertViewHas('orders', fn ($rows) => $rows->modelKeys() === [$order->id]);
        $this->get($url.'?result=none')->assertOk()->assertViewHas('orders', fn ($rows) => $rows->modelKeys() === [$empty->id]);
        $this->get($url.'?result=reviewed')->assertOk()->assertViewHas('orders', fn ($rows) => $rows->isEmpty());
        $report->update(['reviewed_at' => now(), 'review_note' => 'تمت مراجعة التقرير الخارجي']);
        $this->get($url.'?result=reviewed')->assertOk()->assertSee('تمت المراجعة')->assertViewHas('orders', fn ($rows) => $rows->modelKeys() === [$order->id]);
        $this->getJson($url.'?result=unknown')->assertUnprocessable()->assertJsonValidationErrors('result');
    }
}
