<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LogicException;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorDiagnosticOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admins_cannot_open_doctor_orders(): void
    {
        $order = DoctorDiagnosticOrder::factory()->create();

        $this->get(route('doctor.orders.lab'))->assertRedirect(route('doctor.login'));
        $this->get(route('doctor.orders.show', $order))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.orders.radiology'))->assertRedirect(route('doctor.login'));
        $this->putJson(route('doctor.orders.update', $order), [])->assertUnauthorized();

        $this->assertSame(0, $order->revisions()->count());
    }

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    public function test_start_is_idempotent_and_binds_identity_to_the_owned_visit(string $kind): void
    {
        $visit = $this->visit();
        $key = (string) Str::uuid();
        $url = route('doctor.orders.store', ['doctorVisit' => $visit, 'kind' => $kind]);

        $response = $this->postJson($url, ['request_key' => $key, 'patient_name' => 'Injected patient', 'doctor_id' => 999, 'status' => 'approved'])->assertOk();
        $this->postJson($url, ['request_key' => $key])->assertOk()->assertJsonPath('url', $response->json('url'));

        $order = DoctorDiagnosticOrder::sole();
        $this->assertSame($visit->doctor_id, $order->doctor_id);
        $this->assertSame($visit->id, $order->doctor_visit_id);
        $this->assertSame($kind, $order->kind);
        $this->assertSame('draft', $order->status);
        $this->assertNull($order->approved_at);
        $this->assertSame(1, $order->revisions()->count());
        $this->get($response->json('url'))->assertOk()->assertSee($visit->doctorAppointment->patient_name)->assertDontSee('Injected patient');
    }

    public function test_multiple_orders_are_allowed_but_a_reused_key_cannot_target_another_kind_or_visit(): void
    {
        $visit = $this->visit();
        $key = (string) Str::uuid();
        $this->postJson(route('doctor.orders.store', ['doctorVisit' => $visit, 'kind' => 'lab']), ['request_key' => $key])->assertOk();

        $this->postJson(route('doctor.orders.store', ['doctorVisit' => $visit, 'kind' => 'radiology']), ['request_key' => $key])->assertUnprocessable()->assertJsonValidationErrors('request_key');
        $anotherAppointment = DoctorAppointment::factory()->create(['doctor_facility_id' => $visit->doctorAppointment->doctor_facility_id]);
        $anotherVisit = DoctorVisit::factory()->create(['doctor_appointment_id' => $anotherAppointment->id]);
        $this->postJson(route('doctor.orders.store', ['doctorVisit' => $anotherVisit, 'kind' => 'lab']), ['request_key' => $key])->assertUnprocessable()->assertJsonValidationErrors('request_key');
        $this->postJson(route('doctor.orders.store', ['doctorVisit' => $visit, 'kind' => 'lab']), ['request_key' => (string) Str::uuid()])->assertOk();
        $this->postJson(route('doctor.orders.store', ['doctorVisit' => $visit, 'kind' => 'radiology']), ['request_key' => (string) Str::uuid()])->assertOk();

        $this->assertDatabaseCount('doctor_diagnostic_orders', 3);
    }

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    public function test_private_multi_item_order_can_be_approved_without_admin_or_outbound_side_effects(string $kind): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => $kind]);
        $data = $this->payload();
        $data['items'][] = ['name' => 'فحص ثان خاص بالطلب', 'body_area' => 'منطقة مدونة ثانية'];
        Mail::fake();
        Notification::fake();

        $this->putJson(route('doctor.orders.update', $order), $data)->assertOk();

        $order->refresh();
        $this->assertSame('approved', $order->status);
        $this->assertNotNull($order->approved_at);
        $this->assertSame(2, $order->revision);
        $this->assertCount(2, $order->content['items']);
        $this->assertSame('custom', $order->content['items'][0]['source']);
        $this->assertTrue(Str::isUuid($order->content['items'][0]['id']));
        $this->assertNotSame($order->content['items'][0]['id'], $order->content['items'][1]['id']);
        $this->assertSame($visit->doctorAppointment->patient_name, $order->issued_snapshot['patient_name']);
        $this->assertSame(1, $order->revisions()->where('action', 'approved')->count());
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
        $this->assertNull($visit->fresh()->closed_at);
        $this->assertDatabaseCount('doctor_payments', 0);
        $this->assertDatabaseCount('medications', 0);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
        $this->get(route('doctor.orders.show', $order))->assertOk()->assertSee('طلب معتمد — للقراءة فقط')->assertDontSee('id="diagnostic-add-item"', false);
        $this->putJson(route('doctor.orders.update', $order), array_replace($data, ['revision' => 2]))->assertUnprocessable();
    }

    public function test_partial_radiology_draft_can_be_saved_but_approval_requires_region_context_question_and_review(): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'radiology']);
        $draft = ['intent' => 'save', 'revision' => 1, 'priority' => 'normal', 'items' => [['name' => 'تصوير مدون']]];

        $this->putJson(route('doctor.orders.update', $order), $draft)->assertOk();
        $this->putJson(route('doctor.orders.update', $order), array_replace($draft, ['intent' => 'approve', 'revision' => 2]))->assertUnprocessable()->assertJsonValidationErrors(['items.0.body_area', 'clinical_context', 'clinical_question', 'review_confirmed']);

        $this->assertSame('draft', $order->fresh()->status);
        $this->assertSame(2, $order->fresh()->revision);
        $this->assertNull($order->fresh()->approved_at);
    }

    public function test_lab_excludes_hidden_imaging_fields_and_new_orders_have_no_suggestions_from_previous_private_items(): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->putJson(route('doctor.orders.update', $order), $this->payload())->assertOk();
        $new = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);

        $this->get(route('doctor.orders.show', $new))->assertOk()->assertDontSee('فحص خاص بالطلب الأول');

        $this->assertArrayNotHasKey('body_area', $order->fresh()->content['items'][0]);
        $this->assertArrayNotHasKey('clinical_question', $order->fresh()->content);
        $this->assertSame([], $new->content['items']);
    }

    public function test_stale_revision_unknown_item_keys_and_oversized_item_lists_do_not_change_the_draft(): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        $data = $this->payload();

        $this->putJson(route('doctor.orders.update', $order), array_replace($data, ['revision' => 99]))->assertUnprocessable()->assertJsonValidationErrors('revision');
        $data['items'][0]['catalog_id'] = 123;
        $this->putJson(route('doctor.orders.update', $order), $data)->assertUnprocessable()->assertJsonValidationErrors('items.0');
        $data = $this->payload();
        $data['items'] = array_fill(0, 21, ['name' => 'فحص مدون']);
        $this->putJson(route('doctor.orders.update', $order), $data)->assertUnprocessable()->assertJsonValidationErrors('items');

        $this->assertSame(1, $order->fresh()->revision);
        $this->assertSame(0, $order->revisions()->count());
    }

    public function test_other_doctors_cannot_read_write_start_or_list_foreign_orders_and_policy_denies_them(): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        $other = Doctor::factory()->create();
        $this->assertTrue(Gate::forUser($visit->doctor)->allows('view', $order));
        $this->assertTrue(Gate::forUser($visit->doctor)->allows('update', $order));
        $this->assertFalse(Gate::forUser($other)->allows('view', $order));
        $this->assertFalse(Gate::forUser($other)->allows('update', $order));

        $this->actingAs($other, 'doctor')->get(route('doctor.orders.show', $order))->assertNotFound();
        $this->putJson(route('doctor.orders.update', $order), $this->payload())->assertNotFound();
        $this->postJson(route('doctor.orders.store', ['doctorVisit' => $visit, 'kind' => 'lab']), ['request_key' => (string) Str::uuid()])->assertNotFound();
        $this->get(route('doctor.orders.lab'))->assertOk()->assertViewHas('orders', fn ($orders) => $orders->isEmpty());

        $this->assertSame(0, $order->revisions()->count());
    }

    #[TestWith(['verification'])]
    #[TestWith(['membership'])]
    #[TestWith(['facility'])]
    #[TestWith(['arrival'])]
    #[TestWith(['appointment'])]
    #[TestWith(['closed'])]
    public function test_inactive_context_blocks_new_orders_and_draft_updates(string $missing): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        match ($missing) {
            'verification' => $visit->doctor->update(['verification_status' => 'pending']),
            'membership' => $visit->doctorAppointment->doctorFacility->update(['membership_status' => 'suspended']),
            'facility' => $visit->doctorAppointment->doctorFacility->facility->update(['is_active' => false]),
            'arrival' => $visit->doctorAppointment->update(['arrived_at' => null]),
            'appointment' => $visit->doctorAppointment->update(['status' => 'cancelled']),
            'closed' => $visit->update(['status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم محفوظ']]),
        };

        $this->putJson(route('doctor.orders.update', $order), $this->payload())->assertUnprocessable();
        $this->postJson(route('doctor.orders.store', ['doctorVisit' => $visit, 'kind' => 'lab']), ['request_key' => (string) Str::uuid()])->assertUnprocessable();

        $this->assertSame(1, $order->fresh()->revision);
        $this->assertSame(0, $order->revisions()->count());
        $this->assertDatabaseCount('doctor_diagnostic_orders', 1);
    }

    public function test_search_status_and_priority_filters_are_owner_and_kind_scoped(): void
    {
        $visit = $this->visit();
        $visit->doctorAppointment->update(['patient_name' => 'سليم خليل']);
        $match = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'priority' => 'urgent']);
        DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'priority' => 'urgent', 'kind' => 'radiology']);
        DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'priority' => 'normal']);
        DoctorDiagnosticOrder::factory()->create(['priority' => 'urgent']);

        $this->get(route('doctor.orders.lab', ['search' => 'سليم', 'status' => 'draft', 'priority' => 'urgent']))->assertOk()->assertViewHas('orders', fn ($orders) => $orders->count() === 1 && $orders->first()->id === $match->id);
        $this->get(route('doctor.orders.lab', ['status' => 'sent']))->assertSessionHasErrors('status');
    }

    public function test_reading_closed_visit_orders_is_allowed_but_unapproved_drafts_remain_read_only(): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk()->assertSee('بدء طلب مختبر')->assertSee($order->reference())->assertSee('لديك مسودات طلبات فحوصات');
        $visit->update(['status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم محفوظ']]);

        $this->get(route('doctor.orders.show', $order))->assertOk()->assertSee('المسودة للقراءة فقط')->assertDontSee('id="diagnostic-add-item"', false);
    }

    public function test_issued_identity_is_frozen_and_clinical_content_is_escaped_and_not_cached(): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        $data = $this->payload();
        $data['items'][0]['name'] = '<script>test()</script>';
        $this->putJson(route('doctor.orders.update', $order), $data)->assertOk();
        $name = $order->fresh()->issued_snapshot['patient_name'];
        $visit->doctorAppointment->update(['patient_name' => 'Changed appointment identity']);

        $response = $this->get(route('doctor.orders.show', $order))->assertOk()->assertSee($name)->assertDontSee('Changed appointment identity')->assertSee('<script>test()</script>')->assertDontSee('<script>test()</script>', false);

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertSame(1, $order->revisions()->where('action', 'viewed')->count());
    }

    public function test_a_missing_stamp_prevents_silently_approving_a_different_document(): void
    {
        Storage::fake('local');
        $visit = $this->visit();
        $visit->doctor->forceFill(['stamp_path' => 'missing.png'])->save();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);

        $this->putJson(route('doctor.orders.update', $order), $this->payload())->assertUnprocessable()->assertJsonValidationErrors('stamp');

        $this->assertNull($order->fresh()->approved_at);
        $this->assertSame(0, $order->revisions()->count());
    }

    public function test_approved_orders_cannot_be_modified_directly_through_the_model(): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->putJson(route('doctor.orders.update', $order), $this->payload())->assertOk();

        $this->expectException(LogicException::class);
        $order->fresh()->update(['content' => ['items' => []]]);
    }

    private function visit(): DoctorVisit
    {
        $visit = DoctorVisit::factory()->create();
        $visit->doctor->update(['verification_status' => 'verified']);
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $this->actingAs($visit->doctor, 'doctor');

        return $visit;
    }

    public function test_sparse_item_positions_are_normalized_after_removing_a_middle_row(): void
    {
        $visit = $this->visit();
        $order = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id]);
        $data = $this->payload();
        $data['items'] = [5 => ['name' => 'فحص محفوظ بعد حذف سطر']];

        $this->putJson(route('doctor.orders.update', $order), $data)->assertOk();

        $this->assertSame([0], array_keys($order->fresh()->content['items']));
        $this->assertSame('فحص محفوظ بعد حذف سطر', $order->fresh()->content['items'][0]['name']);
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return ['revision' => 1, 'intent' => 'approve', 'priority' => 'urgent', 'clinical_context' => 'سياق سريري مدون للتحقق', 'clinical_question' => 'سؤال سريري مدون للتحقق', 'review_confirmed' => '1', 'items' => [['name' => 'فحص خاص بالطلب الأول', 'body_area' => 'منطقة مدونة']]];
    }
}
