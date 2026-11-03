<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorPrescription;
use App\Models\DoctorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use LogicException;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorPrescriptionCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_retry_creates_one_linked_draft_and_never_rewrites_the_approved_original(): void
    {
        $original = $this->approved();
        $before = $original->getAttributes();

        $response = $this->correct($original)->assertOk();
        $this->correct($original)->assertOk()->assertJsonPath('url', $response->json('url'));

        $child = $original->correction()->sole();
        $this->assertSame(2, $child->document_version);
        $this->assertSame($original->content, $child->content);
        $this->assertNull($child->approved_at);
        $this->assertNull($child->issued_snapshot);
        $this->assertSame('draft', $child->status);
        $this->assertSame($original->doctor_id, $child->doctor_id);
        $this->assertSame('توضيح التعليمات المدونة في الوصفة', $child->correction_reason);
        $this->assertSame($before, $original->fresh()->getAttributes());
        $this->assertDatabaseCount('doctor_prescriptions', 2);
        $this->assertSame(1, $child->revisions()->where('action', 'correction_created')->count());
        $this->assertSame($child->id, $original->doctorVisit->fresh()->prescription->id);
    }

    public function test_correction_approval_preserves_original_patient_identity_and_keeps_both_versions(): void
    {
        $original = $this->approved();
        $before = $original->getAttributes();
        $this->correct($original)->assertOk();
        $child = $original->correction()->sole();
        $original->doctorVisit->doctorAppointment->update(['patient_name' => 'Changed appointment identity']);
        $data = $this->payload();
        $data['notes'] = 'تعليمات مصححة محفوظة';

        $this->putJson(route('doctor.clinical.prescriptions.update', $child), $data)->assertOk();

        $child->refresh();
        $this->assertSame('approved', $child->status);
        $this->assertSame('تعليمات مصححة محفوظة', $child->content['notes']);
        $this->assertSame($original->issued_snapshot['patient_name'], $child->issued_snapshot['patient_name']);
        $this->assertSame($before, $original->fresh()->getAttributes());
        $this->get(route('doctor.clinical.prescriptions.show', $original))->assertOk()->assertSee('نسخة سابقة — للقراءة فقط');
        $this->get(route('doctor.clinical.prescriptions.print', $original))->assertOk()->assertSee('نسخة سابقة — صدر إصدار تصحيحي معتمد');
        $this->get(route('doctor.clinical.prescriptions.print', $child))->assertOk()->assertSee('إصدار تصحيحي رقم 2')->assertSee('تعليمات مصححة محفوظة')->assertDontSee('Changed appointment identity');
        $this->assertDatabaseCount('medications', 0);
        $this->assertDatabaseCount('doctor_payments', 0);
    }

    public function test_unapproved_correction_does_not_supersede_or_disable_the_original(): void
    {
        $original = $this->approved();
        $this->correct($original)->assertOk();
        $child = $original->correction()->sole();

        $this->get(route('doctor.clinical.prescriptions.show', $original))->assertOk()->assertSee('توجد مسودة تصحيح غير معتمدة');
        $this->get(route('doctor.clinical.prescriptions.print', $original))->assertOk()->assertDontSee('نسخة سابقة — صدر إصدار تصحيحي معتمد');
        $this->get(route('doctor.clinical.prescriptions.print', $child))->assertConflict();
    }

    public function test_closed_visit_can_have_a_correction_without_reopening_the_visit_or_appointment(): void
    {
        $original = $this->approved();
        $visit = $original->doctorVisit;
        $visit->update(['status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم سريري محفوظ']]);
        $visit->doctorAppointment->update(['status' => 'completed']);
        $before = $visit->getAttributes();

        $this->correct($original)->assertOk();
        $child = $original->correction()->sole();
        $this->get(route('doctor.clinical.prescriptions.show', $child))->assertOk()->assertSee('اعتماد الوصفة')->assertDontSee('بقيت المسودة غير معتمدة وللقراءة فقط');
        $this->putJson(route('doctor.clinical.prescriptions.update', $child), $this->payload())->assertOk();

        $this->assertSame($before, $visit->fresh()->getAttributes());
        $this->assertSame('completed', $visit->doctorAppointment->fresh()->status);
        $this->assertSame('approved', $child->fresh()->status);
    }

    public function test_reason_and_base_revision_are_required_and_stale_requests_leave_no_draft(): void
    {
        $original = $this->approved();

        $this->postJson(route('doctor.clinical.prescriptions.correct', $original), [])->assertUnprocessable()->assertJsonValidationErrors(['reason', 'revision']);
        $this->postJson(route('doctor.clinical.prescriptions.correct', $original), ['reason' => 'قصير', 'revision' => $original->revision])->assertUnprocessable()->assertJsonValidationErrors('reason');
        $this->postJson(route('doctor.clinical.prescriptions.correct', $original), ['reason' => str_repeat(' ', 15), 'revision' => $original->revision])->assertUnprocessable()->assertJsonValidationErrors('reason');
        $this->postJson(route('doctor.clinical.prescriptions.correct', $original), ['reason' => 'توضيح التعليمات المدونة في الوصفة', 'revision' => 999])->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertDatabaseCount('doctor_prescriptions', 1);
    }

    public function test_guests_admins_and_other_doctors_cannot_create_a_correction(): void
    {
        $original = $this->approved();
        auth('doctor')->logout();
        $this->postJson(route('doctor.clinical.prescriptions.correct', $original), [])->assertUnauthorized();
        $this->actingAs(Admin::factory()->create(), 'admin')->postJson(route('doctor.clinical.prescriptions.correct', $original), [])->assertUnauthorized();
        $this->actingAs(Doctor::factory()->create(), 'doctor')->postJson(route('doctor.clinical.prescriptions.correct', $original), [])->assertNotFound();

        $this->assertDatabaseCount('doctor_prescriptions', 1);
    }

    #[TestWith(['verification'])]
    #[TestWith(['membership'])]
    #[TestWith(['facility'])]
    #[TestWith(['appointment'])]
    public function test_inactive_context_prevents_creating_and_updating_corrections(string $inactive): void
    {
        $original = $this->approved();
        $this->correct($original)->assertOk();
        $child = $original->correction()->sole();
        match ($inactive) {
            'verification' => $original->doctor->update(['verification_status' => 'pending']),
            'membership' => $original->doctorVisit->doctorAppointment->doctorFacility->update(['membership_status' => 'suspended']),
            'facility' => $original->doctorVisit->doctorAppointment->doctorFacility->facility->update(['is_active' => false]),
            'appointment' => $original->doctorVisit->doctorAppointment->update(['status' => 'cancelled']),
        };

        $this->correct($original)->assertUnprocessable();
        $this->putJson(route('doctor.clinical.prescriptions.update', $child), $this->payload())->assertUnprocessable();

        $this->assertSame(1, $child->fresh()->revision);
        $this->assertNull($child->fresh()->approved_at);
        $this->assertDatabaseCount('doctor_prescriptions', 2);
    }

    public function test_draft_cannot_be_the_source_of_a_correction(): void
    {
        $draft = DoctorPrescription::factory()->create();
        $this->actingAs($draft->doctor, 'doctor');

        $this->correct($draft)->assertConflict();

        $this->assertDatabaseCount('doctor_prescriptions', 1);
    }

    public function test_a_third_version_links_to_the_second_and_starting_from_visit_opens_the_latest(): void
    {
        $original = $this->approved();
        $this->correct($original)->assertOk();
        $second = $original->correction()->sole();
        $this->putJson(route('doctor.clinical.prescriptions.update', $second), $this->payload())->assertOk();
        $second->refresh();

        $this->correct($second)->assertOk();
        $third = $second->correction()->sole();
        $this->postJson(route('doctor.clinical.prescriptions.store', $original->doctorVisit))->assertOk()->assertJsonPath('url', route('doctor.clinical.prescriptions.show', $third));

        $this->assertSame(3, $third->document_version);
        $this->assertSame($second->id, $third->corrects_prescription_id);
        $this->assertDatabaseCount('doctor_prescriptions', 3);
    }

    public function test_correction_reason_is_escaped_and_metadata_cannot_be_rewritten(): void
    {
        $original = $this->approved();
        $reason = '<script>reason()</script>';
        $this->postJson(route('doctor.clinical.prescriptions.correct', $original), ['reason' => $reason, 'revision' => $original->revision, 'doctor_id' => 999, 'content' => ['items' => []]])->assertOk();
        $child = $original->correction()->sole();

        $this->get(route('doctor.clinical.prescriptions.show', $child))->assertOk()->assertSee($reason)->assertDontSee($reason, false);
        $this->assertSame($original->doctor_id, $child->doctor_id);
        $this->assertSame($original->content, $child->content);
        $this->expectException(LogicException::class);
        $child->update(['correction_reason' => 'تغيير صامت للسبب']);
    }

    private function approved(): DoctorPrescription
    {
        $visit = DoctorVisit::factory()->create();
        $visit->doctor->update(['verification_status' => 'verified']);
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $this->actingAs($visit->doctor, 'doctor');
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->payload())->assertOk();

        return $prescription->fresh();
    }

    private function correct(DoctorPrescription $prescription): TestResponse
    {
        return $this->postJson(route('doctor.clinical.prescriptions.correct', $prescription), ['reason' => 'توضيح التعليمات المدونة في الوصفة', 'revision' => $prescription->revision]);
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return ['revision' => 1, 'intent' => 'approve', 'review_confirmed' => '1', 'items' => [['source' => 'custom', 'name' => 'دواء مدون للتحقق', 'active_ingredient' => 'مادة مدونة', 'strength' => 'تركيز مدون', 'dosage_form' => 'شكل مدون', 'dose' => 'جرعة مدونة', 'frequency' => 'تكرار مدون', 'duration' => 'مدة مدونة', 'quantity' => 'كمية مدونة']]];
    }
}
