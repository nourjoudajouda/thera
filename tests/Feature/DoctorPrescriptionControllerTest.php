<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorPrescription;
use App\Models\DoctorVisit;
use App\Models\Medication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorPrescriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admins_cannot_open_doctor_prescriptions(): void
    {
        $this->get(route('doctor.clinical.prescriptions'))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.clinical.prescriptions'))->assertRedirect(route('doctor.login'));
    }

    public function test_start_retries_create_one_draft_bound_to_the_visit_patient(): void
    {
        $visit = $this->visit();

        $first = $this->postJson(route('doctor.clinical.prescriptions.store', $visit))->assertOk();
        $this->postJson(route('doctor.clinical.prescriptions.store', $visit))->assertOk()->assertJsonPath('url', $first->json('url'));

        $this->assertDatabaseCount('doctor_prescriptions', 1);
        $prescription = DoctorPrescription::sole();
        $this->assertSame($visit->id, $prescription->doctor_visit_id);
        $this->assertSame($visit->doctor_id, $prescription->doctor_id);
        $this->assertSame(1, $prescription->revisions()->count());
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
        $this->get($first->json('url'))->assertOk()->assertSee($visit->doctorAppointment->patient_name)->assertSee('مسودة غير معتمدة');
    }

    #[TestWith(['pending'])]
    #[TestWith(['rejected'])]
    public function test_unapproved_medication_can_be_saved_but_prevents_prescription_approval(string $status): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $medication = Medication::factory()->create(['doctor_id' => $visit->doctor_id, 'status' => $status]);

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->payload($medication))->assertOk();
        $data = $this->payload($medication, 'approve');
        $data['revision'] = 2;
        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $data)->assertUnprocessable()->assertJsonValidationErrors('items.0.medication_id');

        $this->assertSame('draft', $prescription->fresh()->status);
        $this->assertNull($prescription->fresh()->approved_at);
        $this->assertSame(2, $prescription->fresh()->revision);
        $this->assertSame($medication->name, $prescription->fresh()->content['items'][0]['name']);
        $this->assertSame(1, $prescription->revisions()->where('action', 'saved')->count());
    }

    public function test_admin_approves_definition_then_doctor_approves_immutable_prescription_without_dispensing(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $medication = Medication::factory()->create(['doctor_id' => $visit->doctor_id]);
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->patch(route('admin.approvals.medications.update', $medication), ['decision' => 'approve'])->assertRedirect(route('admin.approvals.index'));
        $this->assertSame('approved', $medication->fresh()->status);
        $this->assertSame($admin->id, $medication->fresh()->decided_by);
        $this->assertNotNull($medication->fresh()->decided_at);
        $this->actingAs($visit->doctor, 'doctor')->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->payload($medication, 'approve'))->assertOk();

        $this->assertSame('approved', $prescription->fresh()->status);
        $this->assertNotNull($prescription->fresh()->approved_at);
        $this->assertSame($visit->doctorAppointment->patient_name, $prescription->fresh()->issued_snapshot['patient_name']);
        $this->assertSame(1, $prescription->revisions()->where('action', 'approved')->count());
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
        $this->assertDatabaseCount('doctor_payments', 0);
        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), array_replace($this->payload($medication), ['revision' => 2]))->assertUnprocessable();
        $this->get(route('doctor.clinical.prescriptions.show', $prescription))->assertOk()->assertSee('معتمدة — للقراءة فقط')->assertDontSee('id="rx-add-item"', false);
    }

    public function test_approval_requires_complete_instructions_and_explicit_doctor_review(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $medication = Medication::factory()->approved()->create();

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), ['revision' => 1, 'intent' => 'approve', 'items' => [['medication_id' => $medication->id]]])->assertUnprocessable()
            ->assertJsonValidationErrors(['items.0.dose', 'items.0.frequency', 'items.0.duration', 'items.0.quantity', 'review_confirmed']);

        $this->assertNull($prescription->fresh()->approved_at);
        $this->assertSame(1, $prescription->fresh()->revision);
    }

    public function test_other_doctors_cannot_read_write_start_or_list_another_doctors_prescriptions(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $other = Doctor::factory()->create();
        $this->assertTrue(Gate::forUser($visit->doctor)->allows('view', $prescription));
        $this->assertFalse(Gate::forUser($other)->allows('update', $prescription));

        $this->actingAs($other, 'doctor')->get(route('doctor.clinical.prescriptions.show', $prescription))->assertNotFound();
        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), [])->assertNotFound();
        $this->postJson(route('doctor.clinical.prescriptions.store', $visit))->assertNotFound();
        $this->get(route('doctor.clinical.prescriptions'))->assertOk()->assertViewHas('prescriptions', fn ($items) => $items->isEmpty());
    }

    public function test_stale_revision_and_foreign_pending_medication_leave_the_draft_unchanged(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $medication = Medication::factory()->create();

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->payload($medication))->assertUnprocessable()->assertJsonValidationErrors('items.0.medication_id');
        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), array_replace($this->payload($medication), ['revision' => 99]))->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertSame([], $prescription->fresh()->content['items']);
        $this->assertSame(1, $prescription->fresh()->revision);
    }

    public function test_doctor_can_no_longer_submit_prescription_medications_as_catalog_proposals(): void
    {
        $this->visit();
        $data = ['name' => 'اسم للتحقق من الحفظ', 'active_ingredient' => 'مادة للتحقق', 'strength' => 'تركيز موثق', 'dosage_form' => 'شكل موثق', 'status' => 'approved', 'patient_name' => 'must not be stored'];

        $this->postJson('/doctor/clinical/medications', $data)->assertNotFound();

        $this->assertDatabaseCount('medications', 0);
    }

    public function test_medication_rejection_requires_reason_and_a_processed_decision_cannot_be_replayed(): void
    {
        $medication = Medication::factory()->create();
        $this->actingAs($medication->doctor, 'doctor')->patch(route('admin.approvals.medications.update', $medication), ['decision' => 'approve'])->assertRedirect(route('admin.login'));
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->patchJson(route('admin.approvals.medications.update', $medication), ['decision' => 'reject'])->assertUnprocessable()->assertJsonValidationErrors('rejection_reason');
        $this->patch(route('admin.approvals.medications.update', $medication), ['decision' => 'reject', 'rejection_reason' => 'تعريف يحتاج تصحيحًا'])->assertRedirect();
        $this->patch(route('admin.approvals.medications.update', $medication), ['decision' => 'approve'])->assertConflict();

        $this->assertSame('rejected', $medication->fresh()->status);
        $this->assertSame('تعريف يحتاج تصحيحًا', $medication->fresh()->rejection_reason);
        $this->assertSame($admin->id, $medication->fresh()->decided_by);
    }

    public function test_closed_visit_keeps_prescription_read_access_but_blocks_writes(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $medication = Medication::factory()->approved()->create();
        $visit->update(['status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم سريري محفوظ']]);

        $this->get(route('doctor.clinical.prescriptions.show', $prescription))->assertOk()->assertSee('بقيت المسودة غير معتمدة');
        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->payload($medication))->assertUnprocessable();

        $this->assertSame(1, $prescription->fresh()->revision);
    }

    private function visit(): DoctorVisit
    {
        $visit = DoctorVisit::factory()->create();
        $visit->doctor->update(['verification_status' => 'verified']);
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $this->actingAs($visit->doctor, 'doctor');

        return $visit;
    }

    public function test_private_medication_can_be_approved_without_admin_and_is_never_added_to_the_catalog(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $data = $this->privatePayload('approve');

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $data)->assertOk();

        $this->assertSame('approved', $prescription->fresh()->status);
        $this->assertSame('custom', $prescription->fresh()->content['items'][0]['source']);
        $this->assertSame('دواء خاص بالوصفة فقط', $prescription->fresh()->content['items'][0]['name']);
        $this->assertNull($prescription->fresh()->content['items'][0]['medication_id']);
        $this->assertDatabaseCount('medications', 0);
        $this->assertSame(1, $prescription->revisions()->where('action', 'approved')->count());
        $this->get(route('doctor.clinical.prescriptions.show', $prescription))->assertOk()->assertSee('دواء خاص بهذه الوصفة')->assertViewHas('medications', fn ($items) => $items->isEmpty());
    }

    public function test_private_medication_is_not_a_search_suggestion_even_for_its_author(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $catalog = Medication::factory()->approved()->create();

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->privatePayload())->assertOk();

        $this->get(route('doctor.clinical.prescriptions.show', $prescription))->assertOk()
            ->assertViewHas('medications', fn ($items) => $items->count() === 1 && $items->sole()->id === $catalog->id)
            ->assertDontSee('اقتراح دواء للمراجعة');
        $this->assertDatabaseCount('medications', 1);
        $this->assertSame('approved', $catalog->fresh()->status);
    }

    public function test_legacy_pending_medication_is_displayed_as_private_without_altering_the_old_catalog_record(): void
    {
        $visit = $this->visit();
        $pending = Medication::factory()->create(['doctor_id' => $visit->doctor_id]);
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id, 'content' => ['items' => [$pending->only(['name', 'active_ingredient', 'strength', 'dosage_form']) + ['medication_id' => $pending->id]]]]);

        $this->get(route('doctor.clinical.prescriptions.show', $prescription))->assertOk()
            ->assertViewHas('displayItems', fn ($items) => $items[0]['source'] === 'custom' && $items[0]['name'] === $pending->name)
            ->assertViewHas('medications', fn ($items) => $items->isEmpty());

        $this->assertSame('pending', $pending->fresh()->status);
        $this->assertSame(1, $prescription->fresh()->revision);
    }

    public function test_private_approval_requires_complete_drug_identity_but_a_partial_draft_can_be_saved(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), ['revision' => 1, 'intent' => 'save', 'items' => [['source' => 'custom', 'name' => 'دواء غير مكتمل']]])->assertOk();
        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), ['revision' => 2, 'intent' => 'approve', 'review_confirmed' => '1', 'items' => [['source' => 'custom', 'name' => 'دواء غير مكتمل']]])
            ->assertUnprocessable()->assertJsonValidationErrors(['items.0.active_ingredient', 'items.0.strength', 'items.0.dosage_form', 'items.0.dose']);

        $this->assertSame('draft', $prescription->fresh()->status);
        $this->assertSame(2, $prescription->fresh()->revision);
        $this->assertDatabaseCount('medications', 0);
    }

    /** @return array<string, mixed> */
    private function privatePayload(string $intent = 'save'): array
    {
        return ['revision' => 1, 'intent' => $intent, 'review_confirmed' => '1', 'items' => [['source' => 'custom', 'name' => 'دواء خاص بالوصفة فقط', 'active_ingredient' => 'مادة موثقة للاختبار', 'strength' => 'تركيز موثق', 'dosage_form' => 'شكل موثق', 'dose' => 'جرعة مدونة للاختبار', 'frequency' => 'تكرار مدون', 'duration' => 'مدة مدونة', 'quantity' => 'كمية مدونة']]];
    }

    public function test_private_drug_fields_are_escaped_and_cannot_mutate_or_reference_a_catalog_entry(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $catalog = Medication::factory()->approved()->create();
        $data = $this->privatePayload();
        $data['items'][0]['medication_id'] = $catalog->id;
        $data['items'][0]['name'] = '<script>alert("private")</script>';

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $data)->assertOk();
        $this->get(route('doctor.clinical.prescriptions.show', $prescription))->assertOk()
            ->assertSee($data['items'][0]['name'])->assertDontSee($data['items'][0]['name'], false);

        $this->assertNull($prescription->fresh()->content['items'][0]['medication_id']);
        $this->assertSame($catalog->name, $catalog->fresh()->name);
        $this->assertDatabaseCount('medications', 1);
    }

    #[TestWith(['verification'])]
    #[TestWith(['membership'])]
    #[TestWith(['facility'])]
    public function test_inactive_clinical_context_rejects_prescription_writes(string $missing): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $medication = Medication::factory()->approved()->create();
        match ($missing) {
            'verification' => $visit->doctor->update(['verification_status' => 'pending']),
            'membership' => $visit->doctorAppointment->doctorFacility->update(['membership_status' => 'suspended']),
            'facility' => $visit->doctorAppointment->doctorFacility->facility->update(['is_active' => false]),
        };
        $this->actingAs($visit->doctor->fresh(), 'doctor');

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->payload($medication))->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertSame(1, $prescription->fresh()->revision);
    }

    public function test_draft_does_not_require_review_and_medical_content_is_escaped_and_not_cached(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $medication = Medication::factory()->approved()->create();
        $data = $this->payload($medication);
        unset($data['review_confirmed']);

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $data)->assertOk();
        $response = $this->get(route('doctor.clinical.prescriptions.show', $prescription))->assertOk()
            ->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false);

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_admin_catalog_review_never_exposes_patient_or_prescription_content(): void
    {
        $visit = $this->visit();
        Medication::factory()->create(['doctor_id' => $visit->doctor_id, 'name' => 'Catalog proposal']);
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')->get(route('admin.approvals.index'))->assertOk()->assertSee('Catalog proposal')->assertDontSee($visit->doctorAppointment->patient_name);
    }

    /** @return array<string, mixed> */
    private function payload(Medication $medication, string $intent = 'save'): array
    {
        return ['revision' => 1, 'intent' => $intent, 'review_confirmed' => '1', 'notes' => 'ملاحظات محفوظة', 'items' => [['medication_id' => $medication->id, 'dose' => 'جرعة مدونة للاختبار', 'frequency' => 'تكرار مدون', 'duration' => 'مدة مدونة', 'quantity' => 'كمية مدونة', 'instructions' => '<script>alert(1)</script>']]];
    }
}
