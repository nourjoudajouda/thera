<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorExternalReportTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    public function test_upload_and_retry_preserve_original_order_and_private_file(string $kind): void
    {
        Storage::fake('local');
        Mail::fake();
        Notification::fake();
        $order = $this->approved($kind);
        $before = $order->fresh()->getAttributes();
        $visit = $order->doctorVisit->getAttributes();
        $data = $this->payload();
        $response = $this->postJson(route('doctor.orders.reports.store', $order), $data)->assertOk();
        $this->postJson(route('doctor.orders.reports.store', $order), $data)->assertOk()->assertJsonPath('url', $response->json('url'));
        $report = DoctorExternalReport::sole();
        Storage::disk('local')->assertExists($report->file_path);
        $this->assertSame(1, count(Storage::disk('local')->allFiles()));
        $this->assertSame($order->id, $report->doctor_diagnostic_order_id);
        $this->assertSame('مركز الرواد', $report->source_name);
        $this->assertNull($report->reviewed_at);
        $this->assertSame($before, $order->fresh()->getAttributes());
        $this->assertSame(collect($visit)->except(['status', 'updated_at'])->all(), collect($order->doctorVisit->fresh()->getAttributes())->except(['status', 'updated_at'])->all());
        $this->assertSame('pending_results', $order->doctorVisit->fresh()->status);
        $this->assertSame(1, $order->revisions()->where('action', 'external_uploaded')->count());
        $this->assertDatabaseCount('doctor_payments', 0);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
        $this->get($response->json('url'))->assertOk()->assertSee('تقرير خارجي — غير معتمد من THERA')->assertSee('مركز الرواد');
        $this->get(route('doctor.orders.reports.download', $report))->assertOk()->assertDownload('external-report-'.$report->id.'.pdf')->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_review_is_immutable_idempotent_and_does_not_reopen_visit(): void
    {
        Storage::fake('local');
        $order = $this->approved();
        $order->doctorVisit->doctorAppointment->update(['arrived_at' => now()]);
        $order->doctorVisit->update(['closed_at' => now(), 'status' => 'completed', 'content' => ['assessment' => 'تقييم محفوظ']]);
        $visit = $order->doctorVisit->fresh()->getAttributes();
        $this->postJson(route('doctor.orders.reports.store', $order), $this->payload())->assertOk();
        $report = DoctorExternalReport::sole();
        $data = ['review_note' => 'تمت قراءة التقرير وتحديد مراجعة سريرية لاحقة.', 'review_confirmed' => 1];
        $this->postJson(route('doctor.orders.reports.review', $report), $data)->assertOk();
        $reviewedAt = $report->fresh()->reviewed_at;
        $this->postJson(route('doctor.orders.reports.review', $report), $data)->assertOk();
        $this->postJson(route('doctor.orders.reports.review', $report), ['review_note' => 'تغيير الملاحظة', 'review_confirmed' => 1])->assertUnprocessable();
        $this->assertTrue($reviewedAt->equalTo($report->fresh()->reviewed_at));
        $this->assertSame($data['review_note'], $report->fresh()->review_note);
        $this->assertSame(1, $order->revisions()->where('action', 'external_reviewed')->count());
        $this->assertSame($visit, $order->doctorVisit->fresh()->getAttributes());
        $this->get(route('doctor.orders.reports.show', $report))->assertOk()->assertSee('تمت المراجعة')->assertSee('غير معتمد من THERA');
    }

    public function test_guests_admins_and_other_doctors_cannot_access_reports(): void
    {
        $report = DoctorExternalReport::factory()->create();
        foreach (['show', 'download'] as $action) {
            $url = route('doctor.orders.reports.'.$action, $report);
            $this->getJson($url)->assertUnauthorized();
            $this->actingAs(Admin::factory()->create(), 'admin')->getJson($url)->assertUnauthorized();
            $this->actingAs(Doctor::factory()->create(), 'doctor')->getJson($url)->assertNotFound();
            auth('doctor')->logout();
        }
        $this->actingAs(Doctor::factory()->create(), 'doctor')->postJson(route('doctor.orders.reports.store', $report->order), $this->payload())->assertNotFound();
        $this->postJson(route('doctor.orders.reports.review', $report), ['review_note' => 'مراجعة غير مصرح بها', 'review_confirmed' => 1])->assertNotFound();
    }

    #[TestWith(['title', ''])]
    #[TestWith(['source_name', ''])]
    #[TestWith(['report_date', '2099-01-01'])]
    #[TestWith(['identity_confirmed', 0])]
    #[TestWith(['request_key', 'invalid'])]
    public function test_invalid_upload_metadata_is_rejected(string $field, mixed $value): void
    {
        Storage::fake('local');
        $order = $this->approved();
        $data = $this->payload();
        $data[$field] = $value;
        $this->postJson(route('doctor.orders.reports.store', $order), $data)->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('doctor_external_reports', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    #[TestWith(['html'])]
    #[TestWith(['extension'])]
    #[TestWith(['size'])]
    public function test_unsafe_or_oversized_files_are_rejected(string $case): void
    {
        Storage::fake('local');
        $order = $this->approved();
        $data = $this->payload();
        $data['report_file'] = match ($case) {
            'html' => UploadedFile::fake()->createWithContent('report.pdf', '<html><script>alert(1)</script></html>'),
            'extension' => UploadedFile::fake()->createWithContent('report.php', "%PDF-1.4\n%%EOF"),
            'size' => UploadedFile::fake()->create('large.pdf', 5121, 'application/pdf'),
        };
        $this->postJson(route('doctor.orders.reports.store', $order), $data)->assertUnprocessable()->assertJsonValidationErrors('report_file');
        $this->assertDatabaseCount('doctor_external_reports', 0);
    }

    #[TestWith(['draft'])]
    #[TestWith(['unverified'])]
    #[TestWith(['membership'])]
    #[TestWith(['facility'])]
    public function test_ineligible_context_cannot_upload(string $case): void
    {
        $order = $case === 'draft' ? DoctorDiagnosticOrder::factory()->create() : $this->approved();
        $this->actingAs($order->doctor, 'doctor');
        if ($case === 'unverified') {
            $order->doctor->update(['verification_status' => 'pending']);
        }
        if ($case === 'membership') {
            $order->doctorVisit->doctorAppointment->doctorFacility->update(['membership_status' => 'suspended']);
        }
        if ($case === 'facility') {
            $order->doctorVisit->doctorAppointment->doctorFacility->facility->update(['is_active' => false]);
        }
        $this->postJson(route('doctor.orders.reports.store', $order), $this->payload())->assertUnprocessable();
        $this->assertDatabaseCount('doctor_external_reports', 0);
    }

    public function test_filters_are_scoped_to_doctor_and_match_kind_review_patient_and_source(): void
    {
        $order = $this->approved();
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id, 'title' => 'تقرير ظاهر', 'source_name' => 'مركز الرواد']);
        $radiology = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $order->doctor_visit_id, 'kind' => 'radiology', 'status' => 'approved', 'approved_at' => now()]);
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $radiology->id, 'title' => 'تقرير أشعة آخر', 'reviewed_at' => now(), 'review_note' => 'مراجعة محفوظة']);
        DoctorExternalReport::factory()->create(['title' => 'تقرير طبيب آخر']);
        $this->get(route('doctor.orders.results', ['search' => 'الرواد', 'kind' => 'lab', 'review' => 'pending']))->assertOk()->assertSee('تقرير ظاهر')->assertDontSee('تقرير أشعة آخر')->assertDontSee('تقرير طبيب آخر');
        $this->get(route('doctor.orders.results', ['review' => 'reviewed']))->assertOk()->assertSee('تقرير أشعة آخر')->assertDontSee('تقرير ظاهر');
        $this->get(route('doctor.orders.results', ['search' => 'سليم']))->assertOk()->assertSee('تقرير ظاهر');
    }

    public function test_missing_or_changed_file_cannot_be_downloaded_or_reviewed(): void
    {
        Storage::fake('local');
        $order = $this->approved();
        $report = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id]);
        $this->get(route('doctor.orders.reports.download', $report))->assertNotFound();
        Storage::disk('local')->put($report->file_path, 'changed bytes');
        $this->get(route('doctor.orders.reports.download', $report))->assertConflict();
        $this->postJson(route('doctor.orders.reports.review', $report), ['review_note' => 'مراجعة التقرير', 'review_confirmed' => 1])->assertConflict();
        $this->assertNull($report->fresh()->reviewed_at);
    }

    public function test_report_metadata_and_notes_are_escaped_and_file_paths_not_exposed(): void
    {
        $order = $this->approved();
        $report = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $order->id, 'title' => '<script>alert(1)</script>', 'source_name' => '<img src=x onerror=alert(2)>', 'reviewed_at' => now(), 'review_note' => '<script>secret()</script>']);
        $this->get(route('doctor.orders.reports.show', $report))->assertOk()->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertDontSee($report->file_path);
        $this->get(route('doctor.orders.results'))->assertOk()->assertDontSee('<img src=x onerror=alert(2)>', false);
    }

    public function test_superseded_order_keeps_its_reports_but_cannot_accept_new_uploads(): void
    {
        Storage::fake('local');
        $order = $this->approved();
        $this->postJson(route('doctor.orders.reports.store', $order), $this->payload())->assertOk();
        $report = DoctorExternalReport::sole();
        $child = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $order->doctor_visit_id, 'corrects_document_id' => $order->id, 'correction_reason' => 'تصحيح اسم الفحص المطلوب', 'document_version' => 2, 'status' => 'approved', 'approved_at' => now()]);
        $this->postJson(route('doctor.orders.reports.store', $order), $this->payload())->assertUnprocessable();
        $this->get(route('doctor.orders.reports.show', $report))->assertOk()->assertSee('مرتبط بإصدار سابق');
        $this->get(route('doctor.orders.results', ['order' => $child->id]))->assertOk()->assertSee('لا توجد تقارير مطابقة');
        $this->assertSame($order->id, $report->fresh()->doctor_diagnostic_order_id);
    }

    public function test_request_key_cannot_rebind_report_to_another_order(): void
    {
        Storage::fake('local');
        $order = $this->approved();
        $data = $this->payload();
        $this->postJson(route('doctor.orders.reports.store', $order), $data)->assertOk();
        $other = DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $order->doctor_visit_id, 'status' => 'approved', 'approved_at' => now()]);
        $this->postJson(route('doctor.orders.reports.store', $other), $data)->assertUnprocessable()->assertJsonValidationErrors('request_key');
        $this->assertDatabaseCount('doctor_external_reports', 1);
    }

    public function test_valid_image_is_downloadable_and_review_requires_note_and_confirmation(): void
    {
        Storage::fake('local');
        $order = $this->approved();
        $data = $this->payload();
        $data['report_file'] = UploadedFile::fake()->image('report.png', 100, 100);
        $this->postJson(route('doctor.orders.reports.store', $order), $data)->assertOk();
        $report = DoctorExternalReport::sole();
        $this->get(route('doctor.orders.reports.download', $report))->assertOk()->assertDownload('external-report-'.$report->id.'.png');
        $this->postJson(route('doctor.orders.reports.review', $report), [])->assertUnprocessable()->assertJsonValidationErrors(['review_note', 'review_confirmed']);
        $order->doctor->update(['verification_status' => 'pending']);
        $this->postJson(route('doctor.orders.reports.review', $report), ['review_note' => 'ملاحظة مراجعة', 'review_confirmed' => 1])->assertUnprocessable();
        $this->assertNull($report->fresh()->reviewed_at);
    }

    public function test_report_identity_and_file_cannot_be_replaced_through_the_model(): void
    {
        $report = DoctorExternalReport::factory()->create();
        $this->expectException(\LogicException::class);
        $report->update(['source_name' => 'جهة مختلفة']);
    }

    private function approved(string $kind = 'lab'): DoctorDiagnosticOrder
    {
        $order = DoctorDiagnosticOrder::factory()->create(['kind' => $kind, 'status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => ['patient_name' => 'سليم خليل', 'facility_name' => 'مركز الشفاء'], 'content' => ['items' => [['name' => 'فحص محفوظ']]]]);
        $order->doctor->update(['verification_status' => 'verified']);
        $this->actingAs($order->doctor, 'doctor');

        return $order;
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return ['title' => 'تقرير الفحوصات', 'source_name' => 'مركز الرواد', 'report_date' => now()->toDateString(), 'request_key' => (string) Str::uuid(), 'identity_confirmed' => 1, 'report_file' => UploadedFile::fake()->createWithContent('report.pdf', "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\n%%EOF")];
    }
}
