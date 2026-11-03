<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientDelegation;
use App\Models\PatientDoctorConsent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientFamilyAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_adult_approval_revoke_and_expiry_control_every_patient_request(): void
    {
        $guardian = Patient::factory()->bookable()->create();
        $adult = Patient::factory()->bookable()->create();
        $this->actingAs($guardian->user, 'sanctum');
        $id = $this->postJson('/api/v1/family/delegations', ['kind' => 'adult', 'relationship' => 'أخت', 'target_public_id' => $adult->public_id])->assertCreated()->json('data.id');
        $url = '/api/v1/patients/'.$adult->public_id.'/appointments';
        $this->getJson($url)->assertNotFound();
        $this->getJson('/api/v1/family/delegations')->assertOk()->assertJsonPath('data.0.patient_name', null);
        $this->patchJson('/api/v1/family/delegations/'.$id, ['action' => 'approve', 'expires_at' => now()->addDays(10)->toIso8601String()])->assertNotFound();
        $this->actingAs($adult->user, 'sanctum')->patchJson('/api/v1/family/delegations/'.$id, ['action' => 'approve', 'expires_at' => now()->addDays(10)->toIso8601String()])->assertOk();
        $this->actingAs($guardian->user, 'sanctum')->getJson($url)->assertOk();
        $this->getJson('/api/v1/patients')->assertOk()->assertJsonCount(2, 'data');
        $this->travel(11)->days();
        $this->getJson($url)->assertNotFound();
        $this->actingAs($adult->user, 'sanctum')->patchJson('/api/v1/family/delegations/'.$id, ['action' => 'revoke'])->assertOk();
        $this->actingAs($guardian->user, 'sanctum')->getJson($url)->assertNotFound();
    }

    public function test_guardian_proof_is_private_and_only_admin_can_activate_it(): void
    {
        Storage::fake('local');
        $guardian = Patient::factory()->bookable()->create();
        $this->actingAs($guardian->user, 'sanctum');
        $id = $this->postJson('/api/v1/family/delegations', ['kind' => 'guardian', 'relationship' => 'ابن', 'name' => 'طفل تجريبي', 'birth_date' => '2020-01-01', 'proof' => UploadedFile::fake()->create('proof.pdf', 10, 'application/pdf')])->assertCreated()->json('data.id');
        $grant = PatientDelegation::findOrFail($id);
        $url = '/api/v1/patients/'.$grant->patient->public_id.'/appointments';
        $this->getJson($url)->assertNotFound();
        $this->getJson('/api/v1/family/delegations')->assertJsonMissingPath('data.0.proof_path');
        $this->patchJson('/api/v1/family/delegations/'.$id, ['action' => 'approve', 'expires_at' => now()->addDays(30)->toIso8601String()])->assertNotFound();
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin')->patch(route('admin.patient-delegations.update', $grant), ['action' => 'approve', 'reason' => 'تمت مراجعة إثبات الولاية', 'expires_at' => now()->addDays(30)->toDateString()])->assertRedirect();
        $this->actingAs($guardian->user, 'sanctum')->getJson($url)->assertOk();
        $this->assertTrue($grant->patient->fresh()->booking_enabled);
        $this->assertDatabaseHas('patient_access_events', ['action' => 'guardian_approve', 'admin_id' => $admin->id]);
    }

    public function test_doctor_consent_is_scoped_to_patient_and_can_be_revoked(): void
    {
        $patient = Patient::factory()->bookable()->create();
        $other = Patient::factory()->bookable()->create();
        $doctor = Doctor::factory()->create();
        $this->actingAs($patient->user, 'sanctum');
        $url = '/api/v1/patients/'.$patient->public_id.'/consents';
        $this->postJson($url, ['doctor_id' => $doctor->id, 'action' => 'grant'])->assertOk()->assertJsonPath('data.0.revoked_at', null);
        $this->assertTrue(PatientDoctorConsent::sole()->expires_at->isSameDay(now()->addDays(30)));
        $this->actingAs($other->user, 'sanctum')->getJson($url)->assertNotFound();
        $this->actingAs($patient->user, 'sanctum')->postJson($url, ['doctor_id' => $doctor->id, 'action' => 'revoke'])->assertOk();
        $this->assertNotNull(PatientDoctorConsent::sole()->revoked_at);
    }
}
