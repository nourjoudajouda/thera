<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorPrescription;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorInlineDocumentTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['prescription'])]
    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    #[TestWith(['referral'])]
    #[TestWith(['followup'])]
    public function test_owned_document_editor_is_bound_to_its_visit_without_page_navigation(string $kind): void
    {
        [$document, $route] = $this->document($kind);
        $visit = $document->doctorVisit;
        $this->actingAs($visit->doctor, 'doctor');
        $response = $this->get(route($route, $document).'?inline=1&inline_visit='.$visit->id)->assertOk()
            ->assertSee('data-inline-kind="'.$kind.'"', false)
            ->assertSee('data-inline-visit="'.$visit->id.'"', false)
            ->assertSee('name="revision"', false)->assertSee('inline-document-save-state', false)
            ->assertDontSee('id="app-sidebar"', false)->assertDontSee('<html', false);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame(1, $document->fresh()->revision);
        $this->assertSame([], $visit->fresh()->content);
        $this->getJson(route($route, $document).'?inline=1')->assertUnprocessable()->assertJsonValidationErrors('inline_visit');
        $this->get(route($route, $document).'?inline=1&inline_visit='.($visit->id + 100))->assertNotFound();
        $this->actingAs(Doctor::factory()->create(), 'doctor')->get(route($route, $document).'?inline=1&inline_visit='.$visit->id)->assertNotFound();
    }

    #[TestWith(['prescription'])]
    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    #[TestWith(['referral'])]
    #[TestWith(['followup'])]
    public function test_approved_or_closed_document_keeps_read_only_protection_inside_popup(string $kind): void
    {
        [$document, $route] = $this->document($kind);
        $visit = $document->doctorVisit;
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $visit->update(['closed_at' => now(), 'status' => 'completed', 'content' => ['assessment' => 'تقييم سريري موثق']]);
        $this->actingAs($visit->doctor, 'doctor')->get(route($route, $document).'?inline=1&inline_visit='.$visit->id)->assertOk()
            ->assertSee('data-inline-readonly="1"', false)->assertSee('fieldset disabled', false)->assertDontSee('value="approve"', false);
    }

    public function test_saving_inline_order_changes_only_the_canonical_document_not_visit_draft(): void
    {
        [$order] = $this->document('lab');
        $visit = $order->doctorVisit;
        $visit->doctor->update(['verification_status' => 'verified']);
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $visit->update(['content' => ['clinical_notes' => 'الملاحظة الأصلية'], 'revision' => 3]);
        $this->actingAs($visit->doctor->fresh(), 'doctor')->putJson(route('doctor.orders.update', $order), [
            'revision' => 1, 'intent' => 'save', 'priority' => 'normal', 'items' => [['name' => 'CBC']], 'clinical_context' => 'مراجعة سريرية',
        ])->assertOk()->assertJsonPath('url', route('doctor.orders.show', $order));
        $this->assertSame('CBC', $order->fresh()->content['items'][0]['name']);
        $this->assertSame(2, $order->fresh()->revision);
        $this->assertSame(3, $visit->fresh()->revision);
        $this->assertSame('الملاحظة الأصلية', $visit->content['clinical_notes']);
        $this->assertDatabaseCount('doctor_diagnostic_orders', 1);
        $this->get(route('doctor.orders.show', $order).'?inline=1&inline_visit='.$visit->id)->assertOk()->assertSee('CBC');
    }

    public function test_visit_exposes_all_five_inline_actions_and_one_shared_popup(): void
    {
        $visit = DoctorVisit::factory()->create();
        $this->actingAs($visit->doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk()
            ->assertSee('data-inline-create', false)->assertSee('clinical-document-modal', false)
            ->assertSee('doctor-visit-documents.js', false)->assertSee('بدء وصفة')->assertSee('بدء طلب مختبر')->assertSee('بدء طلب أشعة')->assertSee('بدء إحالة')->assertSee('بدء خطة متابعة');
    }

    /** @return array{0: Model, 1: string} */
    private function document(string $kind): array
    {
        if ($kind === 'prescription') {
            return [DoctorPrescription::factory()->create(), 'doctor.clinical.prescriptions.show'];
        }
        if (in_array($kind, ['lab', 'radiology'], true)) {
            return [DoctorDiagnosticOrder::factory()->create(['kind' => $kind]), 'doctor.orders.show'];
        }

        return [DoctorReferral::factory()->create(['kind' => $kind]), 'doctor.clinical.referrals.show'];
    }
}
