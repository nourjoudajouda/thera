<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorDocumentCorrectionTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    #[TestWith(['referral'])]
    #[TestWith(['followup'])]
    public function test_retry_creates_one_linked_draft_without_rewriting_or_superseding_original(string $kind): void
    {
        $original = $this->approved($kind);
        $before = $original->fresh()->getAttributes();
        $response = $this->correct($original)->assertOk();
        $this->correct($original)->assertOk()->assertJsonPath('url', $response->json('url'));
        $child = $original->correction()->sole();
        $this->assertSame(2, $child->document_version);
        $this->assertSame($original->content, $child->content);
        $this->assertSame($kind, $child->kind);
        $this->assertSame($original->doctor_visit_id, $child->doctor_visit_id);
        $this->assertNull($child->approved_at);
        $this->assertNull($child->issued_snapshot);
        $this->assertSame('draft', $child->status);
        $this->assertSame(1, $child->revisions()->where('action', 'correction_created')->count());
        $this->assertSame($before, $original->fresh()->getAttributes());
        $this->get($this->route($original, 'show'))->assertOk()->assertSee('توجد مسودة تصحيح غير معتمدة');
        $this->get($this->route($original, 'print'))->assertOk()->assertDontSee('نسخة سابقة — صدر إصدار تصحيحي معتمد');
        $this->get($this->route($child, 'print'))->assertConflict();
    }

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    #[TestWith(['referral'])]
    #[TestWith(['followup'])]
    public function test_approval_keeps_identity_both_versions_and_marks_previous_views_and_prints(string $kind): void
    {
        $original = $this->approved($kind);
        Mail::fake();
        Notification::fake();
        $before = $original->fresh()->getAttributes();
        $this->correct($original)->assertOk();
        $child = $original->correction()->sole();
        $original->doctorVisit->doctorAppointment->update(['patient_name' => 'اسم تغير في الموعد']);
        $this->get($this->route($child, 'show'))->assertOk()->assertSee('المريض المحفوظ')->assertDontSee('اسم تغير في الموعد');
        $data = $this->payload($child);
        $data['instructions'] = 'تعليمات مصححة محفوظة';
        $this->putJson($this->route($child, 'update'), $data)->assertOk();
        $child->refresh();
        $this->assertSame('approved', $child->status);
        $this->assertSame('تعليمات مصححة محفوظة', $child->content['instructions']);
        $this->assertSame('المريض المحفوظ', $child->issued_snapshot['patient_name']);
        $this->assertSame($before, $original->fresh()->getAttributes());
        $this->get($this->route($original, 'show'))->assertOk()->assertSee('نسخة سابقة — صدر إصدار تصحيحي معتمد');
        $this->get($this->route($original, 'print'))->assertOk()->assertSee('نسخة سابقة — صدر إصدار تصحيحي معتمد');
        $this->get($this->route($child, 'print'))->assertOk()->assertSee('إصدار تصحيحي رقم 2')->assertSee('تعليمات مصححة محفوظة')->assertDontSee('اسم تغير في الموعد');
        $this->get($this->route($original, 'index'))->assertOk()->assertSee('نسخة سابقة');
        $this->get(route('doctor.clinical.visit', ['visit' => $original->doctor_visit_id]))->assertOk()->assertSee('نسخة سابقة');
        Mail::assertNothingSent();
        Notification::assertNothingSent();
        $this->assertDatabaseCount('doctor_payments', 0);
        $this->assertDatabaseCount('doctor_appointments', 1);
    }

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    #[TestWith(['referral'])]
    #[TestWith(['followup'])]
    public function test_closed_visit_corrections_do_not_reopen_clinical_or_booking_state(string $kind): void
    {
        $original = $this->approved($kind);
        $visit = $original->doctorVisit;
        $visit->update(['status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم محفوظ']]);
        $visit->doctorAppointment->update(['status' => 'completed']);
        $before = $visit->fresh()->getAttributes();
        $this->correct($original)->assertOk();
        $child = $original->correction()->sole();
        $this->get($this->route($child, 'show'))->assertOk()->assertSee($child instanceof DoctorReferral ? 'اعتماد المستند' : 'اعتماد الطلب');
        $this->putJson($this->route($child, 'update'), $this->payload($child))->assertOk();
        $this->assertSame($before, $visit->fresh()->getAttributes());
        $this->assertSame('completed', $visit->doctorAppointment->fresh()->status);
        $this->assertSame('approved', $child->fresh()->status);
    }

    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_reason_revision_and_approved_source_are_required(string $kind): void
    {
        $document = $this->approved($kind);
        $this->postJson($this->route($document, 'correct'), [])->assertUnprocessable()->assertJsonValidationErrors(['reason', 'revision']);
        $this->postJson($this->route($document, 'correct'), ['reason' => 'قصير', 'revision' => 2])->assertUnprocessable()->assertJsonValidationErrors('reason');
        $this->postJson($this->route($document, 'correct'), ['reason' => str_repeat(' ', 20), 'revision' => 2])->assertUnprocessable()->assertJsonValidationErrors('reason');
        $this->postJson($this->route($document, 'correct'), ['reason' => 'توضيح التعليمات وسبب التصحيح', 'revision' => 999])->assertUnprocessable()->assertJsonValidationErrors('revision');
        $draft = $kind === 'lab' ? DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $document->doctor_visit_id]) : DoctorReferral::factory()->create(['doctor_visit_id' => $document->doctor_visit_id]);
        $this->correct($draft)->assertConflict();
        $this->assertSame(0, $document->correction()->count());
    }

    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_guests_admins_and_other_doctors_cannot_create_corrections(string $kind): void
    {
        $document = $this->approved($kind);
        auth('doctor')->logout();
        $this->correct($document)->assertUnauthorized();
        $this->actingAs(Admin::factory()->create(), 'admin');
        $this->correct($document)->assertUnauthorized();
        $this->actingAs(Doctor::factory()->create(), 'doctor');
        $this->correct($document)->assertNotFound();
        $this->assertSame(0, $document->correction()->count());
    }

    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_successive_corrections_form_a_single_version_chain(string $kind): void
    {
        $original = $this->approved($kind);
        $this->correct($original)->assertOk();
        $second = $original->correction()->sole();
        $this->putJson($this->route($second, 'update'), $this->payload($second))->assertOk();
        $this->correct($second->fresh())->assertOk();
        $third = $second->correction()->sole();
        $this->assertSame(3, $third->document_version);
        $this->assertSame($second->id, $third->corrects_document_id);
        $this->correct($original)->assertOk()->assertJsonPath('url', $this->route($second, 'show'));
    }

    public function test_unchanged_historical_followup_date_can_be_retained_but_new_past_date_is_rejected(): void
    {
        $original = $this->approved('followup');
        $this->travelTo(now()->addDays(20));
        $this->correct($original)->assertOk();
        $child = $original->correction()->sole();
        $data = $this->payload($child);
        $this->putJson($this->route($child, 'update'), array_replace($data, ['due_date' => now()->subDay()->format('Y-m-d')]))->assertUnprocessable()->assertJsonValidationErrors('due_date');
        $data['due_date'] = $original->content['due_date'];
        $this->putJson($this->route($child, 'update'), $data)->assertOk();
        $this->assertSame($original->content['due_date'], $child->fresh()->content['due_date']);
    }

    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_correction_reason_identity_and_version_cannot_be_rewritten(string $kind): void
    {
        $original = $this->approved($kind);
        $this->correct($original)->assertOk();
        $child = $original->correction()->sole();
        $this->expectException(\LogicException::class);
        $child->update(['correction_reason' => 'استبدال السبب المحفوظ']);
    }

    #[TestWith(['lab', 'verification'])]
    #[TestWith(['lab', 'arrival'])]
    #[TestWith(['lab', 'appointment'])]
    #[TestWith(['lab', 'membership'])]
    #[TestWith(['lab', 'facility'])]
    #[TestWith(['referral', 'verification'])]
    #[TestWith(['referral', 'arrival'])]
    #[TestWith(['referral', 'appointment'])]
    #[TestWith(['referral', 'membership'])]
    #[TestWith(['referral', 'facility'])]
    public function test_lost_context_blocks_correction_creation_and_approval(string $kind, string $missing): void
    {
        $document = $this->approved($kind);
        $this->correct($document)->assertOk();
        $child = $document->correction()->sole();
        $visit = $document->doctorVisit;
        match ($missing) {
            'verification' => $visit->doctor->update(['verification_status' => 'pending']),
            'arrival' => $visit->doctorAppointment->update(['arrived_at' => null]),
            'appointment' => $visit->doctorAppointment->update(['status' => 'cancelled']),
            'membership' => $visit->doctorAppointment->doctorFacility->update(['membership_status' => 'suspended']),
            'facility' => $visit->doctorAppointment->doctorFacility->facility->update(['is_active' => false]),
        };
        $this->correct($document)->assertUnprocessable()->assertJsonValidationErrors('revision');
        $this->putJson($this->route($child, 'update'), $this->payload($child))->assertUnprocessable()->assertJsonValidationErrors('revision');
        $this->assertNull($child->fresh()->approved_at);
        $this->assertSame(1, $child->revision);
        $this->assertSame(1, $document->correction()->count());
    }

    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_model_rejects_a_correction_rebound_to_a_different_document_kind(string $kind): void
    {
        $document = $this->approved($kind);
        $this->expectException(\LogicException::class);
        $document->newQuery()->create(['doctor_id' => $document->doctor_id, 'doctor_visit_id' => $document->doctor_visit_id, 'kind' => $kind === 'lab' ? 'radiology' : 'followup', 'document_version' => 2, 'corrects_document_id' => $document->id, 'correction_reason' => 'سبب تصحيح محفوظ', 'content' => []]);
    }

    public function test_correction_reason_is_escaped_in_workspace_and_print(): void
    {
        $document = $this->approved('lab');
        $reason = 'توضيح <script>alert(1)</script> مع سبب محفوظ';
        $this->postJson($this->route($document, 'correct'), ['reason' => $reason, 'revision' => 2])->assertOk();
        $child = $document->correction()->sole();
        $this->get($this->route($child, 'show'))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->putJson($this->route($child, 'update'), $this->payload($child))->assertOk();
        $this->get($this->route($child, 'print'))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    private function approved(string $kind): DoctorDiagnosticOrder|DoctorReferral
    {
        $visit = DoctorVisit::factory()->create();
        $visit->doctor->update(['verification_status' => 'verified']);
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $this->actingAs($visit->doctor, 'doctor');
        $snapshot = ['patient_name' => 'المريض المحفوظ', 'patient_phone' => '+962790000000', 'doctor_name' => 'الطبيب المحفوظ', 'facility_name' => 'المنشأة المحفوظة', 'visit_id' => $visit->id, 'appointment_id' => $visit->doctor_appointment_id, 'visit_date' => '2026/10/22 10:00', 'target_name' => 'جهة محفوظة'];
        $common = ['doctor_visit_id' => $visit->id, 'kind' => $kind, 'status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => $snapshot, 'revision' => 2];
        if (in_array($kind, ['lab', 'radiology'], true)) {
            return DoctorDiagnosticOrder::factory()->create($common + ['content' => ['items' => [['name' => 'فحص محفوظ', 'body_area' => 'الركبة اليمنى']], 'clinical_context' => 'سياق محفوظ', 'clinical_question' => 'سؤال محفوظ']]);
        }

        return DoctorReferral::factory()->create($common + ['target_facility_id' => $visit->doctorAppointment->doctorFacility->facility_id, 'content' => ['title' => 'عنوان محفوظ', 'reason' => 'سبب محفوظ', 'due_date' => now()->addWeek()->format('Y-m-d'), 'followup_kind' => 'clinical', 'time_window' => 'any']]);
    }

    private function correct(DoctorDiagnosticOrder|DoctorReferral $document): TestResponse
    {
        return $this->postJson($this->route($document, 'correct'), ['reason' => 'توضيح التعليمات وسبب التصحيح', 'revision' => $document->revision]);
    }

    /** @return array<string, mixed> */
    private function payload(DoctorDiagnosticOrder|DoctorReferral $document): array
    {
        if ($document instanceof DoctorDiagnosticOrder) {
            return ['revision' => $document->revision, 'intent' => 'approve', 'priority' => 'normal', 'items' => [['name' => 'فحص مصحح', 'body_area' => 'الركبة اليمنى']], 'clinical_context' => 'سياق سريري محفوظ', 'clinical_question' => 'سؤال سريري محفوظ', 'review_confirmed' => 1];
        }

        return ['revision' => $document->revision, 'intent' => 'approve', 'priority' => 'normal', 'title' => 'عنوان مصحح', 'reason' => 'سبب مدون للمستند', 'target' => 'facility:'.$document->target_facility_id, 'due_date' => now()->addWeek()->format('Y-m-d'), 'followup_kind' => 'clinical', 'time_window' => 'any', 'review_confirmed' => 1];
    }

    private function route(DoctorDiagnosticOrder|DoctorReferral $document, string $action): string
    {
        if ($action === 'index') {
            return route($document instanceof DoctorDiagnosticOrder ? 'doctor.orders.'.$document->kind : 'doctor.clinical.referrals');
        }

        return route(($document instanceof DoctorDiagnosticOrder ? 'doctor.orders' : 'doctor.clinical.referrals').'.'.$action, $document);
    }
}
