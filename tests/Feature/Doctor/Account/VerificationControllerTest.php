<?php

namespace Tests\Feature\Doctor\Account;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use App\Models\DoctorVerificationRequest;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_doctor_cannot_access_verification_routes(): void
    {
        $verificationRequest = DoctorVerificationRequest::factory()->create();

        $this->get(route('doctor.account.verification'))
            ->assertRedirect(route('doctor.login'));
        $this->post(route('doctor.account.verification.documents.store'))
            ->assertRedirect(route('doctor.login'));
        $this->post(route('doctor.account.verification.submit'))
            ->assertRedirect(route('doctor.login'));
        $this->get(route('doctor.account.verification.documents.download', [$verificationRequest, DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE]))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_verification_page_displays_the_authenticated_doctors_real_data(): void
    {
        $doctor = Doctor::factory()->create([
            'name' => 'د. ليان محمود',
            'professional_title' => 'استشارية طب أسرة',
            'years_experience' => 8,
            'professional_bio' => 'نبذة مهنية مكتملة.',
        ]);
        DoctorSpecialty::factory()->for($doctor)->create();

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.verification', ['onboarding' => 1]))
            ->assertOk()
            ->assertSee('د. ليان محمود')
            ->assertSee('استشارية طب أسرة')
            ->assertSee('ترخيص مزاولة المهنة')
            ->assertSee('إثبات التخصص الرئيسي');
    }

    public function test_doctor_can_upload_a_required_verification_document(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.verification.documents.store'), [
                'document_type' => DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE,
                'document' => UploadedFile::fake()->create('license.pdf', 500, 'application/pdf'),
            ])->assertRedirect(route('doctor.account.verification', ['onboarding' => 1, 'tab' => 'documents']))
            ->assertSessionHas('status', 'تم رفع المستند بنجاح.');

        $verificationRequest = $doctor->verificationRequests()->firstOrFail();
        $this->assertSame(DoctorVerificationRequest::STATUS_DRAFT, $verificationRequest->status);
        $this->assertSame('license.pdf', $verificationRequest->documents[DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE]['original_name']);
        Storage::disk('local')->assertExists($verificationRequest->documents[DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE]['path']);
    }

    public function test_verification_document_rejects_unsupported_file_types(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')
            ->from(route('doctor.account.verification'))
            ->post(route('doctor.account.verification.documents.store'), [
                'document_type' => DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE,
                'document' => UploadedFile::fake()->create('script.php', 10, 'text/plain'),
            ])->assertRedirect(route('doctor.account.verification'))
            ->assertSessionHasErrors('document');

        $this->assertDatabaseCount('doctor_verification_requests', 0);
    }

    public function test_doctor_cannot_submit_verification_with_missing_requirements(): void
    {
        $doctor = Doctor::factory()->create();
        DoctorVerificationRequest::factory()->for($doctor)->create();

        $this->actingAs($doctor, 'doctor')
            ->from(route('doctor.account.verification'))
            ->post(route('doctor.account.verification.submit'))
            ->assertRedirect(route('doctor.account.verification'))
            ->assertSessionHasErrors('verification');

        $this->assertSame('profile_incomplete', $doctor->fresh()->verification_status);
    }

    public function test_doctor_can_submit_a_complete_verification_request(): void
    {
        $doctor = $this->completeDoctor();
        $verificationRequest = DoctorVerificationRequest::factory()->for($doctor)->submitted()->create([
            'reference' => null,
            'status' => DoctorVerificationRequest::STATUS_DRAFT,
            'submitted_at' => null,
        ]);

        $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.verification.submit'))
            ->assertRedirect(route('doctor.account.verification', ['tab' => 'submission']))
            ->assertSessionHas('status', 'تم إرسال طلب التحقق إلى الإدارة بنجاح.');

        $verificationRequest->refresh();
        $this->assertSame(DoctorVerificationRequest::STATUS_SUBMITTED, $verificationRequest->status);
        $this->assertSame('VR-'.now()->format('Y').'-'.str_pad((string) $verificationRequest->id, 6, '0', STR_PAD_LEFT), $verificationRequest->reference);
        $this->assertNotNull($verificationRequest->submitted_at);
        $this->assertSame('pending', $doctor->fresh()->verification_status);
    }

    public function test_doctor_cannot_download_another_doctors_verification_document(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $otherRequest = DoctorVerificationRequest::factory()->submitted()->create();

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.verification.documents.download', [
                $otherRequest,
                DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE,
            ]))->assertNotFound();
    }

    public function test_uploaded_documents_can_be_submitted_and_reviewed_by_admin(): void
    {
        Storage::fake('local');
        $doctor = $this->completeDoctor();
        $admin = Admin::factory()->create();

        foreach (array_keys(DoctorVerificationRequest::requiredDocumentLabels()) as $type) {
            $this->actingAs($doctor, 'doctor')
                ->post(route('doctor.account.verification.documents.store'), [
                    'document_type' => $type,
                    'document' => UploadedFile::fake()->create($type.'.pdf', 500, 'application/pdf'),
                ])->assertSessionHasNoErrors();
        }

        $this->post(route('doctor.account.verification.submit'))
            ->assertSessionHasNoErrors();

        $verificationRequest = $doctor->verificationRequests()->firstOrFail();
        $this->assertSame(DoctorVerificationRequest::STATUS_SUBMITTED, $verificationRequest->status);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.approvals.index'))
            ->assertOk()
            ->assertSee($verificationRequest->reference);

        $this->patch(route('admin.approvals.update', [
            'type' => 'doctor-verification',
            'id' => $verificationRequest->id,
        ]), ['decision' => 'approve'])->assertSessionHasNoErrors();

        $this->assertSame('verified', $doctor->fresh()->verification_status);
        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.verification'))
            ->assertOk()
            ->assertViewHas('verificationRequest', fn (DoctorVerificationRequest $request): bool => $request->status === DoctorVerificationRequest::STATUS_APPROVED);
    }

    #[TestWith(['approved'])]
    #[TestWith(['submitted'])]
    public function test_locked_verification_blocks_upload_submit_and_restart(string $status): void
    {
        Storage::fake('local');
        $doctor = $this->completeDoctor();
        $original = DoctorVerificationRequest::factory()->for($doctor)->submitted()->create(['status' => $status]);
        $doctor->update(['verification_status' => $status === 'approved' ? 'verified' : 'pending']);

        $this->actingAs($doctor, 'doctor')->from(route('doctor.account.verification'))
            ->post(route('doctor.account.verification.documents.store'), [
                'document_type' => DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE,
                'document' => UploadedFile::fake()->create('new.pdf', 50, 'application/pdf'),
            ])->assertSessionHasErrors('verification');
        $this->post(route('doctor.account.verification.submit'))->assertSessionHasErrors('verification');
        $this->post(route('doctor.account.verification.restart'))->assertSessionHasErrors('verification');

        $this->assertDatabaseCount('doctor_verification_requests', 1);
        $this->assertSame($status, $original->fresh()->status);
        $this->assertSame($status === 'approved' ? 'verified' : 'pending', $doctor->fresh()->verification_status);
        Storage::disk('local')->assertDirectoryEmpty('doctor-verification-documents');
    }

    public function test_pending_verification_blocks_profile_and_primary_specialty_changes(): void
    {
        $doctor = $this->completeDoctor();
        DoctorVerificationRequest::factory()->for($doctor)->submitted()->create();
        $primaryId = $doctor->primarySpecialtyAssignment->specialty_id;
        $replacement = Specialty::factory()->create();

        $this->actingAs($doctor, 'doctor')->from(route('doctor.account.professional-profile'))
            ->put(route('doctor.account.professional-profile.update'), [
                'name' => 'اسم جديد',
                'professional_title' => 'مسمى جديد',
                'years_experience' => 4,
                'professional_bio' => 'نبذة جديدة',
            ])->assertSessionHasErrors('verification');
        $this->post(route('doctor.account.professional-profile.specialties.store'), [
            'type' => 'primary', 'specialty_id' => $replacement->id,
        ])->assertSessionHasErrors('specialty_id');

        $this->assertSame($doctor->name, $doctor->fresh()->name);
        $this->assertSame($primaryId, $doctor->fresh()->primarySpecialtyAssignment->specialty_id);
    }

    public function test_submission_saves_a_snapshot_displayed_to_admin(): void
    {
        $doctor = $this->completeDoctor();
        $doctor->update(['name' => 'الاسم وقت الإرسال']);
        $draft = DoctorVerificationRequest::factory()->for($doctor)->submitted()->create([
            'status' => 'draft', 'reference' => null, 'submitted_at' => null,
        ]);

        $this->actingAs($doctor, 'doctor')->post(route('doctor.account.verification.submit'))
            ->assertSessionHasNoErrors();
        $this->assertSame('الاسم وقت الإرسال', $draft->fresh()->profile_snapshot['name']);
        $this->assertSame($doctor->primarySpecialtyAssignment->specialty_id, $draft->fresh()->profile_snapshot['primary_specialty_id']);

        $doctor->update(['name' => 'تغيير خارجي لاحق']);
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('admin.approvals.index'))
            ->assertOk()->assertSee('الاسم وقت الإرسال');
    }

    public function test_reapplication_keeps_documents_and_rejected_request_in_history(): void
    {
        Storage::fake('local');
        $doctor = $this->completeDoctor();
        $rejected = DoctorVerificationRequest::factory()->for($doctor)->submitted()->create([
            'status' => 'rejected', 'rejection_reason' => 'الترخيص غير واضح.', 'decided_at' => now(),
        ]);
        foreach ($rejected->documents as $document) {
            Storage::disk('local')->put($document['path'], 'original');
        }
        $oldLicense = $rejected->documents[DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE]['path'];
        $oldSpecialty = $rejected->documents[DoctorVerificationRequest::DOCUMENT_PRIMARY_SPECIALTY_PROOF]['path'];

        $this->actingAs($doctor, 'doctor')->post(route('doctor.account.verification.restart'))
            ->assertRedirect(route('doctor.account.verification', ['tab' => 'documents']))
            ->assertSessionHasNoErrors();
        $draft = $doctor->verificationRequests()->where('status', 'draft')->firstOrFail();
        $this->assertSame($rejected->documents, $draft->documents);

        $this->post(route('doctor.account.verification.documents.store'), [
            'document_type' => DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE,
            'document' => UploadedFile::fake()->create('corrected.pdf', 100, 'application/pdf'),
        ])->assertSessionHasNoErrors();
        $this->assertSame($oldSpecialty, $draft->fresh()->documents[DoctorVerificationRequest::DOCUMENT_PRIMARY_SPECIALTY_PROOF]['path']);
        Storage::disk('local')->assertExists($oldLicense);
        $this->assertSame($oldLicense, $rejected->fresh()->documents[DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE]['path']);

        $this->post(route('doctor.account.verification.submit'))->assertSessionHasNoErrors();
        $this->assertSame('submitted', $draft->fresh()->status);
        $this->assertSame('rejected', $rejected->fresh()->status);
        $this->assertDatabaseCount('doctor_verification_requests', 2);
        $this->get(route('doctor.account.verification'))->assertOk()
            ->assertViewHas('verificationHistory', fn ($history): bool => $history->count() === 2)
            ->assertSee($rejected->reference)
            ->assertSee('الترخيص غير واضح.');
    }

    public function test_repeated_restart_reuses_the_same_draft(): void
    {
        $doctor = $this->completeDoctor();
        DoctorVerificationRequest::factory()->for($doctor)->submitted()->create(['status' => 'rejected']);

        $this->actingAs($doctor, 'doctor')->post(route('doctor.account.verification.restart'))->assertSessionHasNoErrors();
        $this->post(route('doctor.account.verification.restart'))->assertSessionHasNoErrors();

        $this->assertDatabaseCount('doctor_verification_requests', 2);
        $this->assertSame(1, $doctor->verificationRequests()->where('status', 'draft')->count());
    }

    private function completeDoctor(): Doctor
    {
        $doctor = Doctor::factory()->create([
            'professional_title' => 'استشاري طب أسرة',
            'years_experience' => 8,
            'professional_bio' => 'نبذة مهنية مكتملة لاختبار طلب التحقق.',
            'profile_completed_at' => now(),
        ]);
        DoctorSpecialty::factory()->for($doctor)->create();

        return $doctor;
    }
}
