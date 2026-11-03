<?php

namespace Tests\Feature;

use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorVisitWorkflowStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_is_derived_from_saved_documentation_and_cannot_be_chosen_by_request(): void
    {
        $visit = $this->visit();
        $this->putJson(route('doctor.clinical.visits.update', $visit), ['revision' => 1, 'intent' => 'save', 'status' => 'completed'])->assertOk()->assertJsonPath('redirect', false);
        $this->assertSame('draft', $visit->fresh()->status);
        $this->assertNull($visit->closed_at);
        $this->putJson(route('doctor.clinical.visits.update', $visit), ['revision' => 2, 'intent' => 'save', 'complaint' => 'شكوى محفوظة', 'status' => 'pending_results'])->assertOk();
        $this->assertSame('initial', $visit->fresh()->status);
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk()->assertSee('يحددها النظام تلقائيًا')->assertDontSee('name="status"', false);
    }

    public function test_only_approved_orders_wait_for_reports_and_review_does_not_overwrite_visit_documentation_or_revision(): void
    {
        Storage::fake('local');
        $visit = $this->visit();
        $visit->update(['content' => ['assessment' => 'تقييم محفوظ'], 'status' => 'initial', 'revision' => 4]);
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->putJson(route('doctor.orders.update', $order), ['revision' => 1, 'intent' => 'save', 'priority' => 'normal', 'items' => [['name' => 'CBC']]])->assertOk();
        $this->assertSame('initial', $visit->fresh()->status);
        $this->putJson(route('doctor.orders.update', $order), ['revision' => 2, 'intent' => 'approve', 'review_confirmed' => 1, 'priority' => 'normal', 'clinical_context' => 'تقييم سريري', 'items' => [['name' => 'CBC']]])->assertOk();
        $this->assertSame('pending_results', $visit->fresh()->status);
        $report = $this->upload($order);
        $this->assertSame('pending_results', $visit->fresh()->status);
        $this->postJson(route('doctor.orders.reports.review', $report), ['review_note' => 'تمت مراجعة التقرير', 'review_confirmed' => 1])->assertOk();
        $this->assertSame('initial', $visit->fresh()->status);
        $this->assertSame(4, $visit->revision);
        $this->assertSame(['assessment' => 'تقييم محفوظ'], $visit->content);
        $this->assertSame('approved', $order->fresh()->status);
        $this->assertSame(2, $visit->revisions()->where('action', 'status_updated')->count());
        $this->upload($order);
        $this->assertSame('pending_results', $visit->fresh()->status);
    }

    public function test_every_current_approved_order_must_have_reviewed_reports_before_waiting_state_clears(): void
    {
        $visit = $this->visit();
        $lab = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'approved_at' => now(), 'status' => 'approved']);
        $radiology = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'radiology', 'approved_at' => now(), 'status' => 'approved']);
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $lab->id, 'reviewed_at' => now()]);
        $this->assertSame('pending_results', $visit->workflowStatus());
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $radiology->id, 'reviewed_at' => now()]);
        $this->assertSame('initial', $visit->workflowStatus());
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $lab->id]);
        $this->assertSame('pending_results', $visit->workflowStatus());
    }

    public function test_superseded_order_does_not_keep_visit_waiting_after_corrected_order_is_reviewed(): void
    {
        $visit = $this->visit();
        $old = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'approved_at' => now(), 'status' => 'approved']);
        $correction = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'approved_at' => now(), 'status' => 'approved', 'corrects_document_id' => $old->id, 'document_version' => 2, 'correction_reason' => 'تصحيح موثق']);
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $correction->id, 'reviewed_at' => now()]);
        $this->assertSame('initial', $visit->workflowStatus());
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function test_confirmed_visit_close_returns_to_daily_appointments_and_is_never_an_automatic_close(bool $json): void
    {
        $visit = $this->visit();
        $payload = ['revision' => 1, 'intent' => 'close', 'assessment' => 'تقييم سريري موثق'];
        $url = route('doctor.clinical.visits.update', $visit);
        $destination = route('doctor.appointments.index', ['view' => 'list']);
        if ($json) {
            $this->putJson($url, $payload)->assertOk()->assertJsonPath('redirect', true)->assertJsonPath('url', $destination);
        } else {
            $this->put($url, $payload)->assertRedirect($destination);
        }
        $this->assertSame('completed', $visit->fresh()->status);
        $this->assertNotNull($visit->fresh()->closed_at);
        $this->assertSame('completed', $visit->doctorAppointment->fresh()->status);
    }

    private function visit(): DoctorVisit
    {
        $visit = DoctorVisit::factory()->create();
        $visit->doctor->update(['verification_status' => 'verified']);
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $this->actingAs($visit->doctor->fresh(), 'doctor');

        return $visit;
    }

    private function upload(DoctorDiagnosticOrder $order): DoctorExternalReport
    {
        $this->postJson(route('doctor.orders.reports.store', $order), [
            'title' => 'تقرير خارجي', 'source_name' => 'مختبر الرواد', 'report_date' => now()->toDateString(), 'identity_confirmed' => 1,
            'request_key' => (string) Str::uuid(), 'report_file' => UploadedFile::fake()->image('report.png', 50, 50),
        ])->assertOk();

        return $order->externalReports()->first();
    }
}
