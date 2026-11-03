<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorReferralControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admins_cannot_access_the_doctor_referral_workspace(): void
    {
        $this->get(route('doctor.clinical.referrals'))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.clinical.referrals'))->assertRedirect(route('doctor.login'));
    }

    public function test_start_is_idempotent_and_bound_to_the_owned_visit_and_kind(): void
    {
        $visit = $this->visit();
        $key = (string) Str::uuid();
        $route = route('doctor.clinical.referrals.store', ['doctorVisit' => $visit, 'kind' => 'referral']);
        $first = $this->postJson($route, ['request_key' => $key])->assertOk();
        $this->postJson($route, ['request_key' => $key])->assertOk()->assertJsonPath('url', $first->json('url'));
        $this->assertDatabaseCount('doctor_referrals', 1);
        $document = DoctorReferral::sole();
        $this->assertSame($visit->doctor_id, $document->doctor_id);
        $this->assertSame($visit->id, $document->doctor_visit_id);
        $this->assertSame(1, $document->revisions()->count());
        $this->postJson(route('doctor.clinical.referrals.store', ['doctorVisit' => $visit, 'kind' => 'followup']), ['request_key' => $key])->assertUnprocessable()->assertJsonValidationErrors('request_key');
        $this->postJson($route, ['request_key' => (string) Str::uuid()])->assertOk();
        $this->assertDatabaseCount('doctor_referrals', 2);
    }

    public function test_referral_approval_freezes_registered_target_without_booking_sending_or_access_grant(): void
    {
        $visit = $this->visit();
        $target = Doctor::factory()->create(['name' => 'الطبيب المحال إليه', 'verification_status' => 'verified']);
        DoctorFacility::factory()->create(['doctor_id' => $target->id]);
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        Mail::fake();
        Notification::fake();
        $appointmentCount = DoctorAppointment::count();
        $this->putJson(route('doctor.clinical.referrals.update', $document), $this->payload('doctor:'.$target->id))->assertOk();
        $document->refresh();
        $this->assertSame('approved', $document->status);
        $this->assertSame($target->id, $document->target_doctor_id);
        $this->assertNull($document->target_facility_id);
        $this->assertSame('الطبيب المحال إليه', $document->issued_snapshot['target_name']);
        $this->assertSame($visit->doctorAppointment->patient_name, $document->issued_snapshot['patient_name']);
        $this->assertSame($appointmentCount, DoctorAppointment::count());
        $this->assertNull($visit->fresh()->closed_at);
        $this->assertSame(1, $document->revisions()->where('action', 'approved')->count());
        $target->update(['name' => 'اسم تغيّر بعد الاعتماد']);
        $this->get(route('doctor.clinical.referrals.show', $document))->assertOk()->assertSee('الطبيب المحال إليه')->assertDontSee('اسم تغيّر بعد الاعتماد')->assertDontSee('name="intent"', false)->assertHeader('Cache-Control', 'no-store, private');
        $this->putJson(route('doctor.clinical.referrals.update', $document), $this->payload('doctor:'.$target->id) + ['revision' => 2])->assertUnprocessable();
        $this->actingAs($target, 'doctor')->get(route('doctor.clinical.referrals.show', $document))->assertNotFound();
        $this->assertFalse(Gate::forUser($target)->allows('view', $document));
        Mail::assertNothingSent();
        Notification::assertNothingSent();
        $this->assertDatabaseCount('doctor_payments', 0);
    }

    public function test_only_active_registered_facilities_can_be_saved_as_a_target(): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $facility = Facility::factory()->create(['is_active' => true]);
        $this->putJson(route('doctor.clinical.referrals.update', $document), $this->payload('facility:'.$facility->id))->assertOk();
        $this->assertSame($facility->id, $document->fresh()->target_facility_id);
        $this->assertNull($document->fresh()->target_doctor_id);
    }

    #[TestWith(['unregistered'])]
    #[TestWith(['nonexistent'])]
    #[TestWith(['inactive'])]
    #[TestWith(['unverified'])]
    #[TestWith(['unaffiliated'])]
    #[TestWith(['suspended'])]
    #[TestWith(['self'])]
    public function test_unavailable_or_manual_targets_are_rejected_without_changing_the_draft(string $case): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $target = 'اسم خارج النظام';
        if ($case === 'nonexistent') {
            $target = 'facility:999999';
        } elseif ($case === 'inactive') {
            $target = 'facility:'.Facility::factory()->create(['is_active' => false])->id;
        } elseif ($case === 'self') {
            $target = 'doctor:'.$visit->doctor_id;
        } elseif (in_array($case, ['unverified', 'unaffiliated', 'suspended'], true)) {
            $targetDoctor = Doctor::factory()->create(['verification_status' => $case === 'unverified' ? 'pending' : 'verified']);
            if ($case !== 'unaffiliated') {
                DoctorFacility::factory()->create(['doctor_id' => $targetDoctor->id, 'membership_status' => $case === 'suspended' ? 'suspended' : 'active']);
            }
            $target = 'doctor:'.$targetDoctor->id;
        }
        $this->putJson(route('doctor.clinical.referrals.update', $document), $this->payload($target))->assertUnprocessable()->assertJsonValidationErrors('target');
        $this->assertSame('draft', $document->fresh()->status);
        $this->assertSame(1, $document->revision);
        $this->assertNull($document->approved_at);
    }

    public function test_followup_requires_a_future_or_current_date_and_does_not_create_a_booking(): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'followup']);
        $payload = $this->payload('facility:9999') + ['due_date' => now()->addWeek()->format('Y-m-d'), 'followup_kind' => 'after_result', 'time_window' => 'morning'];
        $this->putJson(route('doctor.clinical.referrals.update', $document), array_replace($payload, ['due_date' => now()->subDay()->format('Y-m-d')]))->assertUnprocessable()->assertJsonValidationErrors('due_date');
        $this->putJson(route('doctor.clinical.referrals.update', $document), $payload)->assertOk();
        $document->refresh();
        $this->assertNull($document->target_doctor_id);
        $this->assertNull($document->target_facility_id);
        $this->assertSame('after_result', $document->content['followup_kind']);
        $this->assertDatabaseCount('doctor_appointments', 1);
        $this->get(route('doctor.clinical.referrals.show', $document))->assertOk()->assertSee('ليست حجز موعد');
    }

    public function test_incomplete_draft_is_allowed_but_approval_requires_review_and_required_fields(): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->putJson(route('doctor.clinical.referrals.update', $document), ['revision' => 1, 'intent' => 'save', 'priority' => 'normal', 'title' => 'مسودة جزئية'])->assertOk();
        $this->assertSame('مسودة جزئية', $document->fresh()->content['title']);
        $this->putJson(route('doctor.clinical.referrals.update', $document), ['revision' => 2, 'intent' => 'approve', 'priority' => 'normal'])->assertUnprocessable()->assertJsonValidationErrors(['title', 'reason', 'target', 'review_confirmed']);
        $this->assertNull($document->fresh()->approved_at);
    }

    public function test_target_is_rechecked_after_a_draft_was_saved_and_revision_is_enforced(): void
    {
        $visit = $this->visit();
        $facility = Facility::factory()->create();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $payload = $this->payload('facility:'.$facility->id);
        $this->putJson(route('doctor.clinical.referrals.update', $document), array_replace($payload, ['intent' => 'save']))->assertOk();
        $this->putJson(route('doctor.clinical.referrals.update', $document), $payload)->assertUnprocessable()->assertJsonValidationErrors('revision');
        $facility->update(['is_active' => false]);
        $this->putJson(route('doctor.clinical.referrals.update', $document), array_replace($payload, ['revision' => 2]))->assertUnprocessable()->assertJsonValidationErrors('target');
        $this->assertNull($document->fresh()->approved_at);
    }

    public function test_other_doctors_cannot_read_update_or_start_documents_for_the_visit(): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'content' => ['title' => 'عنوان خاص بالطبيب الأول']]);
        $this->actingAs(Doctor::factory()->create(), 'doctor');
        $this->get(route('doctor.clinical.referrals.show', $document))->assertNotFound();
        $this->putJson(route('doctor.clinical.referrals.update', $document), [])->assertNotFound();
        $this->postJson(route('doctor.clinical.referrals.store', ['doctorVisit' => $visit, 'kind' => 'referral']), ['request_key' => (string) Str::uuid()])->assertNotFound();
        $this->get(route('doctor.clinical.referrals', ['search' => 'عنوان خاص']))->assertOk()->assertDontSee('عنوان خاص بالطبيب الأول');
    }

    public function test_closed_visit_blocks_new_documents_and_existing_drafts_become_readonly(): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk()->assertSee('بدء إحالة')->assertSee('لديك مسودات إحالات أو متابعات');
        $visit->update(['status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم مدون']]);
        $this->postJson(route('doctor.clinical.referrals.store', ['doctorVisit' => $visit, 'kind' => 'referral']), ['request_key' => (string) Str::uuid()])->assertUnprocessable();
        $this->putJson(route('doctor.clinical.referrals.update', $document), $this->payload('facility:1'))->assertUnprocessable();
        $this->get(route('doctor.clinical.referrals.show', $document))->assertOk()->assertSee('المسودة للقراءة فقط')->assertDontSee('name="intent"', false);
    }

    public function test_filters_are_grouped_scoped_and_render_clinical_input_escaped(): void
    {
        $visit = $this->visit();
        $first = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'content' => ['title' => '<script>alert(1)</script>']]);
        DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'followup', 'content' => ['title' => 'متابعة لا تطابق النوع']]);
        DoctorReferral::factory()->create(['content' => ['title' => 'عنوان طبيب آخر']]);
        $this->get(route('doctor.clinical.referrals', ['kind' => 'referral', 'status' => 'draft', 'search' => 'script']))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('متابعة لا تطابق النوع')->assertDontSee('عنوان طبيب آخر');
        $this->get(route('doctor.clinical.referrals.show', $first))->assertOk()->assertSee('&lt;script&gt;', false);
        $this->assertSame(1, $first->revisions()->where('action', 'viewed')->count());
    }

    #[TestWith(['verification'])]
    #[TestWith(['arrival'])]
    #[TestWith(['appointment'])]
    #[TestWith(['membership'])]
    #[TestWith(['facility'])]
    public function test_invalid_visit_context_blocks_both_start_and_save(string $missing): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        match ($missing) {
            'verification' => $visit->doctor->update(['verification_status' => 'pending']),
            'arrival' => $visit->doctorAppointment->update(['arrived_at' => null]),
            'appointment' => $visit->doctorAppointment->update(['status' => 'cancelled']),
            'membership' => $visit->doctorAppointment->doctorFacility->update(['membership_status' => 'suspended']),
            'facility' => $visit->doctorAppointment->doctorFacility->facility->update(['is_active' => false]),
        };
        $this->postJson(route('doctor.clinical.referrals.store', ['doctorVisit' => $visit, 'kind' => 'referral']), ['request_key' => (string) Str::uuid()])->assertUnprocessable()->assertJsonValidationErrors('revision');
        $this->putJson(route('doctor.clinical.referrals.update', $document), ['revision' => 1, 'intent' => 'save', 'priority' => 'normal'])->assertUnprocessable()->assertJsonValidationErrors('revision');
        $this->assertSame(1, $document->fresh()->revision);
        $this->assertDatabaseCount('doctor_referrals', 1);
    }

    public function test_untrusted_owner_and_patient_fields_cannot_rebind_the_document(): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $other = DoctorVisit::factory()->create();
        $this->putJson(route('doctor.clinical.referrals.update', $document), ['revision' => 1, 'intent' => 'save', 'priority' => 'normal', 'doctor_id' => $other->doctor_id, 'doctor_visit_id' => $other->id, 'patient_name' => 'مريض آخر', 'kind' => 'followup'])->assertOk();
        $this->assertSame($visit->doctor_id, $document->fresh()->doctor_id);
        $this->assertSame($visit->id, $document->doctor_visit_id);
        $this->assertSame('referral', $document->kind);
        $this->assertArrayNotHasKey('patient_name', $document->content);
    }

    public function test_approved_model_cannot_be_changed_directly(): void
    {
        $document = DoctorReferral::factory()->create(['approved_at' => now(), 'status' => 'approved']);
        $this->expectException(\LogicException::class);
        $document->update(['content' => ['title' => 'استبدال صامت']]);
    }

    public function test_missing_stamp_blocks_approval_without_partial_persistence(): void
    {
        $visit = $this->visit();
        Storage::fake('local');
        $visit->doctor->forceFill(['stamp_path' => 'doctor-stamps/missing.png'])->save();
        $facility = Facility::factory()->create();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->putJson(route('doctor.clinical.referrals.update', $document), $this->payload('facility:'.$facility->id))->assertUnprocessable()->assertJsonValidationErrors('stamp');
        $this->assertNull($document->fresh()->approved_at);
        $this->assertSame(1, $document->revision);
        $this->assertSame(0, $document->revisions()->count());
    }

    public function test_registry_choices_do_not_include_unverified_doctors_or_inactive_facilities(): void
    {
        $visit = $this->visit();
        $document = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
        $eligible = Doctor::factory()->create(['name' => 'طبيب متاح للإحالة', 'verification_status' => 'verified']);
        DoctorFacility::factory()->create(['doctor_id' => $eligible->id]);
        Doctor::factory()->create(['name' => 'طبيب غير موثق مخفي', 'verification_status' => 'pending']);
        Facility::factory()->create(['name' => 'منشأة موقوفة مخفية', 'is_active' => false]);
        $this->get(route('doctor.clinical.referrals.show', $document))->assertOk()->assertSee('طبيب متاح للإحالة')->assertDontSee('طبيب غير موثق مخفي')->assertDontSee('منشأة موقوفة مخفية');
    }

    private function visit(): DoctorVisit
    {
        $visit = DoctorVisit::factory()->create();
        $visit->doctor->update(['verification_status' => 'verified']);
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $this->actingAs($visit->doctor, 'doctor');

        return $visit;
    }

    /** @return array<string, mixed> */
    private function payload(string $target): array
    {
        return ['revision' => 1, 'intent' => 'approve', 'priority' => 'normal', 'title' => 'طلب تقييم تخصصي', 'reason' => 'استكمال التقييم السريري', 'clinical_summary' => 'ملخص مدون مرتبط بالزيارة', 'target' => $target, 'review_confirmed' => 1];
    }
}
