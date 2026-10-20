<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\DoctorAchievement;
use App\Models\DoctorCredential;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApprovalDocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_credential_document(): void
    {
        Storage::fake('local');
        $admin = Admin::factory()->create();
        $credential = DoctorCredential::factory()->create([
            'document_path' => 'doctor-documents/credential.pdf',
            'document_original_name' => 'certificate.pdf',
        ]);
        Storage::disk('local')->put($credential->document_path, 'credential');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.approvals.credentials.document', $credential))
            ->assertOk()
            ->assertDownload('certificate.pdf');
    }

    public function test_admin_can_download_achievement_evidence(): void
    {
        Storage::fake('local');
        $admin = Admin::factory()->create();
        $achievement = DoctorAchievement::factory()->create([
            'evidence_path' => 'doctor-documents/achievement.pdf',
            'evidence_original_name' => 'evidence.pdf',
        ]);
        Storage::disk('local')->put($achievement->evidence_path, 'evidence');

        $this->actingAs($admin, 'admin')
            ->get(route('admin.approvals.achievements.evidence', $achievement))
            ->assertOk()
            ->assertDownload('evidence.pdf');
    }

    public function test_doctor_cannot_download_documents_through_admin_routes(): void
    {
        $credential = DoctorCredential::factory()->create();

        $this->actingAs($credential->doctor, 'doctor')
            ->get(route('admin.approvals.credentials.document', $credential))
            ->assertRedirect(route('admin.login'));
    }
}
