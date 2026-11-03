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
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorDocumentPrintControllerTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    #[TestWith(['referral'])]
    #[TestWith(['followup'])]
    public function test_approved_preview_and_print_request_are_audited_without_changing_the_document(string $kind): void
    {
        $document = $this->document($kind);
        $this->actingAs($document->doctor, 'doctor');
        $attributes = $document->fresh()->getAttributes();
        Mail::fake();
        Notification::fake();
        $preview = $this->get($this->url($document))->assertOk()->assertSee('المريض المحفوظ')->assertSee('الطبيب المحفوظ')->assertSee('الجهة المحفوظة')->assertSee('طباعة / حفظ PDF')->assertSee('هذه النسخة بدون ختم.');
        $preview->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertHeader('Referrer-Policy', 'no-referrer');
        $this->assertStringContainsString('no-store', $preview->headers->get('Cache-Control'));
        $this->postJson($this->url($document, true))->assertOk()->assertJsonPath('message', 'سُجل طلب فتح نافذة الطباعة، وليس تأكيد الطباعة الفعلية.');
        $this->assertSame(1, $document->revisions()->where('action', 'print_previewed')->count());
        $entry = $document->revisions()->where('action', 'print_requested')->sole();
        $this->assertSame($document->doctor_id, $entry->doctor_id);
        $this->assertSame(2, $entry->revision);
        $this->assertSame($attributes, $document->fresh()->getAttributes());
        $this->assertNull($document->doctorVisit->closed_at);
        $this->assertDatabaseCount('doctor_appointments', 1);
        $this->assertDatabaseCount('doctor_payments', 0);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    #[TestWith(['referral'])]
    #[TestWith(['followup'])]
    public function test_print_uses_frozen_identity_and_escaped_saved_content_not_live_profiles(string $kind): void
    {
        $document = $this->document($kind);
        $this->actingAs($document->doctor, 'doctor');
        $document->doctor->update(['name' => 'اسم الطبيب الحالي المتغير']);
        $document->doctorVisit->doctorAppointment->update(['patient_name' => 'اسم المريض الحالي المتغير']);
        $this->get($this->url($document))->assertOk()->assertSee('المريض المحفوظ')->assertDontSee('اسم المريض الحالي المتغير')->assertDontSee('اسم الطبيب الحالي المتغير')->assertSee('&lt;script&gt;', false)->assertDontSee('<script>unsafe()</script>', false);
    }

    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_guests_admins_and_foreign_doctors_cannot_preview_or_record_print_requests(string $kind): void
    {
        $document = $this->document($kind);
        $this->get($this->url($document))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->postJson($this->url($document, true))->assertUnauthorized();
        $this->actingAs(Doctor::factory()->create(), 'doctor')->get($this->url($document))->assertNotFound();
        $this->postJson($this->url($document, true))->assertNotFound();
        $this->assertSame(0, $document->revisions()->count());
    }

    #[TestWith(['lab', 'draft'])]
    #[TestWith(['referral', 'draft'])]
    #[TestWith(['lab', 'missing_snapshot'])]
    #[TestWith(['referral', 'missing_snapshot'])]
    public function test_unapproved_or_incomplete_documents_cannot_be_printed_or_audited(string $kind, string $state): void
    {
        $visit = DoctorVisit::factory()->create();
        $attributes = ['doctor_visit_id' => $visit->id, 'kind' => $kind];
        if ($state === 'missing_snapshot') {
            $attributes += ['status' => 'approved', 'approved_at' => now()];
        }
        $document = $kind === 'lab' ? DoctorDiagnosticOrder::factory()->create($attributes) : DoctorReferral::factory()->create($attributes);
        $this->actingAs($visit->doctor, 'doctor')->get($this->url($document))->assertConflict();
        $this->postJson($this->url($document, true))->assertConflict();
        $this->assertSame(0, $document->revisions()->count());
    }

    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_closed_visit_keeps_print_access_to_approved_documents(string $kind): void
    {
        $document = $this->document($kind);
        $visit = $document->doctorVisit;
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $visit->update(['status' => 'completed', 'closed_at' => now(), 'content' => ['assessment' => 'تقييم محفوظ']]);
        $this->actingAs($document->doctor, 'doctor')->get($this->url($document))->assertOk();
        $this->postJson($this->url($document, true))->assertOk();
        $this->assertSame('completed', $visit->fresh()->status);
    }

    public function test_order_print_contains_all_tests_body_area_context_and_instructions(): void
    {
        $document = $this->document('radiology');
        $this->actingAs($document->doctor, 'doctor')->get($this->url($document))->assertOk()->assertSee('الفحص الأول')->assertSee('الفحص الثاني')->assertSee('الركبة اليمنى')->assertSee('سؤال سريري محفوظ')->assertSee('تعليمات الفحص')->assertSee('سياق سريري محفوظ');
    }

    public function test_referral_print_shows_frozen_target_and_followup_print_shows_suggested_date(): void
    {
        $referral = $this->document('referral');
        $this->actingAs($referral->doctor, 'doctor')->get($this->url($referral))->assertOk()->assertSee('الهدف المحفوظ')->assertSee('سبب محفوظ');
        $followup = $this->document('followup');
        $this->actingAs($followup->doctor, 'doctor')->get($this->url($followup))->assertOk()->assertSee('2026-11-02')->assertSee('بعد ظهور نتيجة')->assertSee('صباحًا')->assertSee('لا تنشئ موعدًا محجوزًا');
    }

    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_saved_stamp_and_logo_are_used_even_if_the_current_stamp_file_is_missing(string $kind): void
    {
        $image = 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/doctor/thera-logo.png')));
        $document = $kind === 'lab' ? DoctorDiagnosticOrder::factory()->create(['status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => ['stamp_data_uri' => $image, 'logo_data_uri' => $image]]) : DoctorReferral::factory()->create(['status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => ['stamp_data_uri' => $image, 'logo_data_uri' => $image]]);
        $document->doctor->forceFill(['stamp_path' => 'missing-current-stamp.png'])->save();
        $this->actingAs($document->doctor, 'doctor')->get($this->url($document))->assertOk()->assertSee($image, false)->assertDontSee('missing-current-stamp.png')->assertDontSee('هذه النسخة بدون ختم.');
    }

    private function document(string $kind): DoctorDiagnosticOrder|DoctorReferral
    {
        $snapshot = ['patient_name' => 'المريض المحفوظ', 'patient_phone' => '+962790000000', 'doctor_name' => 'الطبيب المحفوظ', 'professional_title' => 'طبيب عام', 'facility_name' => 'الجهة المحفوظة', 'target_name' => 'الهدف المحفوظ', 'visit_id' => 7, 'visit_date' => '2026/10/22 10:00'];
        $attributes = ['kind' => $kind, 'status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => $snapshot, 'revision' => 2];
        if (in_array($kind, ['lab', 'radiology'], true)) {
            return DoctorDiagnosticOrder::factory()->create($attributes + ['content' => ['items' => [['name' => 'الفحص الأول', 'body_area' => 'الركبة اليمنى', 'instructions' => 'تعليمات الفحص'], ['name' => 'الفحص الثاني']], 'clinical_context' => 'سياق سريري محفوظ', 'clinical_question' => 'سؤال سريري محفوظ', 'instructions' => '<script>unsafe()</script>']]);
        }

        return DoctorReferral::factory()->create($attributes + ['content' => ['title' => 'عنوان محفوظ', 'reason' => 'سبب محفوظ', 'clinical_summary' => '<script>unsafe()</script>', 'due_date' => '2026-11-02', 'followup_kind' => 'after_result', 'time_window' => 'morning']]);
    }

    private function url(DoctorDiagnosticOrder|DoctorReferral $document, bool $store = false): string
    {
        $prefix = $document instanceof DoctorDiagnosticOrder ? 'doctor.orders' : 'doctor.clinical.referrals';

        return route($prefix.'.print'.($store ? '.store' : ''), $document);
    }
}
