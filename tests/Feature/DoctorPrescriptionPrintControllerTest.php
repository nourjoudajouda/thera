<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorPrescription;
use App\Models\DoctorSpecialty;
use App\Models\DoctorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorPrescriptionPrintControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admins_cannot_upload_read_or_print(): void
    {
        $prescription = DoctorPrescription::factory()->create();
        $this->get(route('doctor.clinical.prescriptions.print', $prescription))->assertRedirect(route('doctor.login'));
        $this->post(route('doctor.account.stamp.store'))->assertRedirect(route('doctor.login'));
        $this->get(route('doctor.account.stamp.show'))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->post(route('doctor.clinical.prescriptions.print.store', $prescription))->assertRedirect(route('doctor.login'));
        $this->assertSame(0, $prescription->revisions()->count());
    }

    public function test_stamp_is_private_and_only_changes_the_authenticated_doctor(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $other = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')->post(route('doctor.account.stamp.store'), ['stamp' => UploadedFile::fake()->image('stamp.png', 180, 100), 'doctor_id' => $other->id], ['Accept' => 'application/json'])->assertOk();

        Storage::disk('local')->assertExists($doctor->fresh()->stamp_path);
        $this->assertNull($other->fresh()->stamp_path);
        $this->assertArrayNotHasKey('stamp_path', $doctor->fresh()->toArray());
        $this->get(route('doctor.account.stamp.show'))->assertOk()->assertHeader('Content-Type', 'image/png')->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->actingAs($other, 'doctor')->get(route('doctor.account.stamp.show', ['doctor_id' => $doctor->id]))->assertNotFound();
    }

    #[TestWith(['svg'])]
    #[TestWith(['text'])]
    #[TestWith(['dimensions'])]
    #[TestWith(['size'])]
    public function test_invalid_stamp_leaves_the_current_stamp_unchanged(string $invalid): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $doctor->forceFill(['stamp_path' => 'existing.png'])->save();
        $file = match ($invalid) {
            'svg' => UploadedFile::fake()->createWithContent('stamp.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            'text' => UploadedFile::fake()->createWithContent('stamp.png', 'not an image'),
            'dimensions' => UploadedFile::fake()->image('stamp.png', 2100, 80),
            'size' => UploadedFile::fake()->image('stamp.png', 180, 100)->size(1025),
        };

        $this->actingAs($doctor, 'doctor')->post(route('doctor.account.stamp.store'), ['stamp' => $file], ['Accept' => 'application/json'])->assertUnprocessable()->assertJsonValidationErrors('stamp');

        $this->assertSame('existing.png', $doctor->fresh()->stamp_path);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_approval_freezes_stamp_specialty_logo_and_identity_for_print(): void
    {
        Storage::fake('local');
        $visit = $this->visit();
        DoctorSpecialty::factory()->create(['doctor_id' => $visit->doctor_id, 'type' => 'primary', 'verification_status' => 'verified']);
        $this->uploadStamp();
        $prescription = $this->approve($visit);
        $snapshot = $prescription->issued_snapshot;
        $this->assertStringStartsWith('data:image/png;base64,', $snapshot['stamp_data_uri']);
        $this->assertStringStartsWith('data:image/png;base64,', $snapshot['logo_data_uri']);
        $this->assertNotEmpty($snapshot['specialty']);
        $this->assertNotEmpty($snapshot['visit_date']);
        $visit->doctor->update(['name' => 'New doctor name']);
        $visit->doctorAppointment->update(['patient_name' => 'New patient name']);
        $this->uploadStamp('new.jpg');

        $response = $this->get(route('doctor.clinical.prescriptions.print', $prescription))->assertOk()
            ->assertSee($snapshot['doctor_name'])->assertSee($snapshot['patient_name'])->assertSee($snapshot['stamp_data_uri'], false)
            ->assertDontSee('New doctor name')->assertDontSee('New patient name')->assertSee('طباعة / حفظ PDF');

        $this->assertSame($snapshot, $prescription->fresh()->issued_snapshot);
        $this->assertSame(2, $prescription->fresh()->revision);
        $this->assertSame(1, $prescription->revisions()->where('action', 'print_previewed')->count());
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response->assertHeader('Referrer-Policy', 'no-referrer')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_draft_and_foreign_prescriptions_cannot_be_printed_or_audited(): void
    {
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);

        $this->get(route('doctor.clinical.prescriptions.print', $prescription))->assertConflict();
        $this->postJson(route('doctor.clinical.prescriptions.print.store', $prescription))->assertConflict();
        $this->actingAs(Doctor::factory()->create(), 'doctor')->get(route('doctor.clinical.prescriptions.print', $prescription))->assertNotFound();
        $this->postJson(route('doctor.clinical.prescriptions.print.store', $prescription))->assertNotFound();

        $this->assertSame(0, $prescription->revisions()->count());
    }

    public function test_legacy_approved_prescription_never_uses_the_current_stamp(): void
    {
        Storage::fake('local');
        $visit = $this->visit();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id, 'status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => ['patient_name' => 'Saved patient', 'doctor_name' => 'Saved doctor']]);
        $this->uploadStamp();

        $this->get(route('doctor.clinical.prescriptions.print', $prescription))->assertOk()->assertSee('هذه النسخة بدون ختم.')->assertDontSee('class="document-stamp"', false);

        $this->assertArrayNotHasKey('stamp_data_uri', $prescription->fresh()->issued_snapshot);
    }

    public function test_closed_visit_keeps_approved_print_access_without_changing_clinical_or_financial_state(): void
    {
        $visit = $this->visit();
        $prescription = $this->approve($visit);
        $visit->update(['closed_at' => now(), 'status' => 'completed', 'content' => ['assessment' => 'تقييم سريري محفوظ']]);

        $this->get(route('doctor.clinical.prescriptions.print', $prescription))->assertOk();
        $this->postJson(route('doctor.clinical.prescriptions.print.store', $prescription))->assertOk()->assertJsonPath('message', 'سُجل طلب فتح نافذة الطباعة، وليس تأكيد الطباعة الفعلية.');

        $entry = $prescription->revisions()->where('action', 'print_requested')->sole();
        $this->assertSame($visit->doctor_id, $entry->doctor_id);
        $this->assertSame(2, $entry->revision);
        $this->assertSame(2, $prescription->fresh()->revision);
        $this->assertSame('approved', $prescription->fresh()->status);
        $this->assertSame('confirmed', $visit->doctorAppointment->fresh()->status);
        $this->assertDatabaseCount('doctor_payments', 0);
    }

    public function test_missing_stamp_blocks_approval_instead_of_silently_omitting_it(): void
    {
        Storage::fake('local');
        $visit = $this->visit();
        $visit->doctor->forceFill(['stamp_path' => 'missing.png'])->save();
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);

        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->payload())->assertUnprocessable()->assertJsonValidationErrors('stamp');

        $this->assertNull($prescription->fresh()->approved_at);
        $this->assertSame(1, $prescription->fresh()->revision);
        $this->assertSame(0, $prescription->revisions()->count());
    }

    public function test_print_escapes_saved_clinical_content(): void
    {
        $visit = $this->visit();
        $visit->doctorAppointment->update(['patient_name' => '<script>patient()</script>']);
        $prescription = $this->approve($visit);

        $this->get(route('doctor.clinical.prescriptions.print', $prescription))->assertOk()
            ->assertSee('<script>patient()</script>')->assertDontSee('<script>patient()</script>', false)
            ->assertSee('<script>notes()</script>')->assertDontSee('<script>notes()</script>', false);
    }

    private function visit(): DoctorVisit
    {
        $visit = DoctorVisit::factory()->create();
        $visit->doctor->update(['verification_status' => 'verified']);
        $visit->doctorAppointment->update(['arrived_at' => now()]);
        $this->actingAs($visit->doctor, 'doctor');

        return $visit;
    }

    private function uploadStamp(string $name = 'stamp.png'): void
    {
        $this->post(route('doctor.account.stamp.store'), ['stamp' => UploadedFile::fake()->image($name, 180, 100)], ['Accept' => 'application/json'])->assertOk();
    }

    private function approve(DoctorVisit $visit): DoctorPrescription
    {
        $prescription = DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
        $this->putJson(route('doctor.clinical.prescriptions.update', $prescription), $this->payload())->assertOk();

        return $prescription->fresh();
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return ['revision' => 1, 'intent' => 'approve', 'review_confirmed' => '1', 'notes' => '<script>notes()</script>', 'items' => [['source' => 'custom', 'name' => 'دواء للتحقق من العرض', 'active_ingredient' => 'مادة موثقة', 'strength' => 'تركيز موثق', 'dosage_form' => 'شكل موثق', 'dose' => 'جرعة مدونة', 'frequency' => 'تكرار مدون', 'duration' => 'مدة مدونة', 'quantity' => 'كمية مدونة']]];
    }
}
