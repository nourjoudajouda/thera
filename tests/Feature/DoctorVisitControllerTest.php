<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorPayment;
use App\Models\DoctorQueueEntry;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorVisitControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admins_cannot_access_doctor_visits(): void
    {
        $this->get(route('doctor.clinical.visits'))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.clinical.visits'))->assertRedirect(route('doctor.login'));
    }

    public function test_start_retries_create_one_visit_without_completing_the_appointment(): void
    {
        $appointment = $this->readyAppointment();

        $first = $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 1])->assertOk();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 1])->assertOk()->assertJsonPath('url', $first->json('url'));

        $this->assertDatabaseCount('doctor_visits', 1);
        $visit = DoctorVisit::sole();
        $this->assertSame('draft', $visit->status);
        $this->assertSame('confirmed', $appointment->fresh()->status);
        $this->assertSame(1, $visit->revisions()->where('action', 'started')->count());
        $this->assertSame(1, $appointment->events()->where('action', 'visit_started')->count());
        $this->assertDatabaseCount('doctor_payments', 1);
    }

    #[TestWith(['arrival'])]
    #[TestWith(['payment'])]
    #[TestWith(['call'])]
    #[TestWith(['date'])]
    #[TestWith(['status'])]
    #[TestWith(['verification'])]
    #[TestWith(['membership'])]
    #[TestWith(['facility'])]
    #[TestWith(['revision'])]
    public function test_start_is_rejected_when_a_required_context_is_missing(string $missing): void
    {
        $appointment = $this->readyAppointment();
        match ($missing) {
            'arrival' => $appointment->update(['arrived_at' => null]),
            'payment' => DoctorPayment::query()->whereBelongsTo($appointment)->update(['amount_minor' => 1]),
            'call' => DoctorQueueEntry::query()->whereBelongsTo($appointment)->delete(),
            'date' => $appointment->update(['starts_at' => now()->subDay()]),
            'status' => $appointment->update(['status' => 'cancelled']),
            'verification' => $appointment->doctor->update(['verification_status' => 'pending']),
            'membership' => $appointment->doctorFacility->update(['membership_status' => 'suspended']),
            'facility' => $appointment->doctorFacility->facility->update(['is_active' => false]),
            default => null,
        };

        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => $missing === 'revision' ? 999 : 1])->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertDatabaseCount('doctor_visits', 0);
        $this->assertDatabaseCount('doctor_visit_revisions', 0);
        $this->assertSame(0, $appointment->events()->where('action', 'visit_started')->count());
    }

    public function test_draft_saves_preserve_versions_without_completing_the_appointment(): void
    {
        $visit = $this->startVisit();

        $this->putJson(route('doctor.clinical.visits.update', $visit), ['revision' => 1, 'intent' => 'save', 'status' => 'draft', 'complaint' => 'الشكوى الأولى'])->assertOk();
        $this->putJson(route('doctor.clinical.visits.update', $visit), ['revision' => 2, 'intent' => 'save', 'status' => 'pending_results', 'complaint' => 'مراجعة الشكوى'])->assertOk();

        $this->assertSame('initial', $visit->fresh()->status);
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
        $this->assertSame('الشكوى الأولى', $visit->revisions()->where('revision', 2)->sole()->snapshot['content']['complaint']);
        $this->assertSame(3, $visit->fresh()->revision);
    }

    public function test_closing_saves_submitted_content_and_completes_the_appointment(): void
    {
        $visit = $this->startVisit();

        $this->putJson(route('doctor.clinical.visits.update', $visit), $this->document() + ['revision' => 1, 'intent' => 'close'])->assertOk();

        $this->assertSame('completed', $visit->fresh()->status);
        $this->assertNotNull($visit->fresh()->closed_at);
        $this->assertSame('مراجعة بعد أسبوع', $visit->fresh()->content['followup_plan']);
        $this->assertSame('completed', $visit->doctorAppointment->fresh()->status);
        $this->assertSame(1, $visit->doctorAppointment->events()->where('action', 'visit_completed')->count());
        $this->assertSame(1, $visit->revisions()->where('action', 'completed')->count());
        $this->assertDatabaseCount('doctor_payments', 1);
        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk()->assertSee('التوثيق للقراءة فقط')->assertDontSee('name="intent"', false);
        $this->putJson(route('doctor.clinical.visits.update', $visit), $this->document() + ['revision' => 2, 'intent' => 'save'])->assertUnprocessable();
        $this->assertSame(2, $visit->fresh()->revision);
    }

    public function test_missing_close_fields_and_stale_revisions_leave_saved_data_unchanged(): void
    {
        $visit = $this->startVisit();

        $this->putJson(route('doctor.clinical.visits.update', $visit), ['revision' => 1, 'intent' => 'close', 'status' => 'draft'])->assertUnprocessable()->assertJsonValidationErrors(['assessment', 'diagnosis']);
        $this->putJson(route('doctor.clinical.visits.update', $visit), $this->document() + ['revision' => 99, 'intent' => 'save'])->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertSame([], $visit->fresh()->content);
        $this->assertSame('draft', $visit->fresh()->status);
        $this->assertSame(1, $visit->revisions()->count());
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
    }

    public function test_other_doctors_cannot_read_write_or_start_the_visit(): void
    {
        $visit = $this->startVisit();
        $other = Doctor::factory()->create();
        $this->assertTrue(Gate::forUser($visit->doctor)->allows('view', $visit));
        $this->assertFalse(Gate::forUser($other)->allows('view', $visit));
        $this->actingAs($other, 'doctor');

        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertNotFound();
        $this->putJson(route('doctor.clinical.visits.update', $visit), $this->document() + ['revision' => 1, 'intent' => 'save'])->assertNotFound();
        $this->postJson(route('doctor.clinical.visits.start', $visit->doctorAppointment), ['revision' => 2])->assertNotFound();
        $this->get(route('doctor.clinical.visits'))->assertOk()->assertViewHas('visits', fn ($items) => $items->isEmpty());
        $this->assertSame(1, $visit->revisions()->count());
    }

    public function test_cancellation_is_blocked_after_start_and_clinical_markup_is_escaped(): void
    {
        $visit = $this->startVisit();
        $script = '<script>alert(1)</script>';

        $this->patchJson(route('doctor.appointments.status', $visit->doctorAppointment), ['action' => 'cancelled', 'revision' => 2, 'reason' => 'طلب إلغاء'])->assertUnprocessable();
        $this->putJson(route('doctor.clinical.visits.update', $visit), ['intent' => 'save', 'revision' => 1, 'status' => 'draft', 'complaint' => $script])->assertOk();

        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk()->assertSee($script)->assertDontSee($script, false);
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
        $this->assertSame(1, $visit->revisions()->where('action', 'viewed')->count());
    }

    public function test_inactive_membership_preserves_read_access_but_blocks_writes(): void
    {
        $visit = $this->startVisit();
        $visit->doctorAppointment->doctorFacility->update(['membership_status' => 'suspended']);

        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk();
        $this->putJson(route('doctor.clinical.visits.update', $visit), $this->document() + ['revision' => 1, 'intent' => 'save'])->assertUnprocessable();

        $this->assertSame([], $visit->fresh()->content);
    }

    #[TestWith(['assessment'])]
    #[TestWith(['diagnosis'])]
    public function test_a_clear_assessment_or_provisional_diagnosis_is_sufficient_to_close(string $field): void
    {
        $visit = $this->startVisit();

        $this->putJson(route('doctor.clinical.visits.update', $visit), ['revision' => 1, 'intent' => 'close', 'status' => 'initial', $field => 'تقييم سريري واضح موثق', 'diagnosis_type' => 'initial'])->assertOk();

        $this->assertSame('completed', $visit->doctorAppointment->fresh()->status);
        $this->assertSame('completed', $visit->fresh()->status);
    }

    public function test_clinical_sections_are_saved_versioned_and_visible_after_reopening(): void
    {
        $visit = $this->startVisit();
        $sections = ['medical_history' => 'تاريخ مرضي موثق من المقابلة', 'physical_exam' => 'نتائج الفحص السريري المدونة', 'treatment_plan' => 'الخطة العلاجية المتفق عليها'];

        $this->putJson(route('doctor.clinical.visits.update', $visit), $this->document() + $sections + ['revision' => 1, 'intent' => 'save'])->assertOk();

        foreach ($sections as $field => $value) {
            $this->assertSame($value, $visit->fresh()->content[$field]);
            $this->assertSame($value, $visit->revisions()->where('revision', 2)->sole()->snapshot['content'][$field]);
        }
        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk()
            ->assertSee('التاريخ المرضي')->assertSee('الفحص السريري')->assertSee('الخطة العلاجية')
            ->assertSee($sections['medical_history'])->assertSee($sections['physical_exam'])->assertSee($sections['treatment_plan']);
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
    }

    #[TestWith(['medical_history'])]
    #[TestWith(['physical_exam'])]
    #[TestWith(['treatment_plan'])]
    public function test_overlong_clinical_sections_do_not_change_the_saved_visit(string $field): void
    {
        $visit = $this->startVisit();

        $this->putJson(route('doctor.clinical.visits.update', $visit), $this->document() + [$field => str_repeat('a', 10001), 'revision' => 1, 'intent' => 'save'])
            ->assertUnprocessable()->assertJsonValidationErrors($field)
            ->assertJsonPath('errors.'.$field.'.0', 'النص أطول من الحد المسموح.');

        $this->assertSame([], $visit->fresh()->content);
        $this->assertSame(1, $visit->fresh()->revision);
    }

    public function test_quick_documentation_keeps_every_field_and_saved_optional_content(): void
    {
        $visit = $this->startVisit();
        $visit->update(['content' => ['medical_history' => 'تاريخ مرضي محفوظ', 'vitals' => ['pulse' => 75]]]);

        $page = $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertOk();

        foreach (['complaint', 'assessment', 'diagnosis', 'diagnosis_type', 'treatment_plan', 'medical_history', 'physical_exam', 'clinical_notes', 'followup_plan', 'vitals[pulse]', 'vitals[temperature]', 'vitals[systolic]', 'vitals[diastolic]', 'vitals[weight]', 'vitals[height]'] as $field) {
            $page->assertSee('name="'.$field.'"', false);
        }
        $page->assertSee('تاريخ مرضي محفوظ')->assertSee('توجد معلومات محفوظة')->assertSee('مراجعة وإغلاق الزيارة')->assertSee('id="visit-review"', false)->assertSee('الإجراءات المرتبطة')->assertDontSee('name="status"', false);
    }

    private function readyAppointment(): DoctorAppointment
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-16 10:00', config('thera.schedule_timezone')));
        $appointment = DoctorAppointment::factory()->create(['starts_at' => now()->subHour(), 'ends_at' => now()->subMinutes(30), 'arrived_at' => now()->subMinutes(65)]);
        $appointment->doctor->update(['verification_status' => 'verified']);
        DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id, 'amount_minor' => $appointment->price_minor]);
        DoctorQueueEntry::factory()->create(['doctor_appointment_id' => $appointment->id]);
        $this->actingAs($appointment->doctor, 'doctor');

        return $appointment;
    }

    private function startVisit(): DoctorVisit
    {
        $appointment = $this->readyAppointment();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 1])->assertOk();

        return DoctorVisit::sole();
    }

    /** @return array<string, string> */
    private function document(): array
    {
        return ['status' => 'draft', 'complaint' => 'شكوى المريض المسجلة', 'assessment' => 'تقييم الطبيب المدون', 'diagnosis' => 'تشخيص مبدئي موثق', 'diagnosis_type' => 'initial', 'followup_plan' => 'مراجعة بعد أسبوع'];
    }
}
