<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorCredential;
use App\Models\DoctorFacility;
use App\Models\DoctorSpecialty;
use App\Models\DoctorVerificationRequest;
use App\Models\Facility;
use App\Models\FacilityProposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_cannot_view_or_process_approvals(): void
    {
        $specialty = DoctorSpecialty::factory()->create();

        $this->get(route('admin.approvals.index'))
            ->assertRedirect(route('admin.login'));

        $this->patch(route('admin.approvals.update', ['type' => 'specialty', 'id' => $specialty->id]), [
            'decision' => 'approve',
        ])->assertRedirect(route('admin.login'));

        $this->assertSame(DoctorSpecialty::STATUS_PENDING, $specialty->fresh()->verification_status);
    }

    public function test_approval_page_displays_only_pending_requests(): void
    {
        $admin = Admin::factory()->create();
        $pending = DoctorCredential::factory()->create(['title' => 'مؤهل بانتظار المراجعة']);
        $verificationRequest = DoctorVerificationRequest::factory()->submitted()->create();
        DoctorCredential::factory()->verified()->create(['title' => 'مؤهل معتمد سابقًا']);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.approvals.index'))
            ->assertOk()
            ->assertSee('class="nav thera-tabs"', escape: false)
            ->assertSee('id="verifications-tab"', escape: false)
            ->assertSee($verificationRequest->reference)
            ->assertSee($pending->title)
            ->assertDontSee('مؤهل معتمد سابقًا');
    }

    public function test_approval_page_displays_a_clear_empty_state_when_there_are_no_requests(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get(route('admin.approvals.index'))
            ->assertOk()
            ->assertSee('لا توجد طلبات اعتماد')
            ->assertDontSee("include('admin.approvals.partials.empty')");
    }

    public function test_admin_can_approve_a_specialty(): void
    {
        $admin = Admin::factory()->create();
        $specialty = DoctorSpecialty::factory()->create();

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.approvals.update', ['type' => 'specialty', 'id' => $specialty->id]), [
                'decision' => 'approve',
            ])->assertRedirect(route('admin.approvals.index'))
            ->assertSessionHas('status', 'تم اعتماد الطلب بنجاح.');

        $this->assertSame(DoctorSpecialty::STATUS_VERIFIED, $specialty->fresh()->verification_status);
    }

    public function test_admin_can_approve_a_doctor_verification_request(): void
    {
        $admin = Admin::factory()->create();
        $verificationRequest = DoctorVerificationRequest::factory()->submitted()->create();

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.approvals.update', ['type' => 'doctor-verification', 'id' => $verificationRequest->id]), [
                'decision' => 'approve',
            ])->assertRedirect(route('admin.approvals.index'))
            ->assertSessionHas('status', 'تم اعتماد الطلب بنجاح.');

        $verificationRequest->refresh();
        $this->assertSame(DoctorVerificationRequest::STATUS_APPROVED, $verificationRequest->status);
        $this->assertSame($admin->id, $verificationRequest->decided_by);
        $this->assertNotNull($verificationRequest->decided_at);
        $this->assertSame('verified', $verificationRequest->doctor->verification_status);
    }

    public function test_admin_can_reject_a_doctor_verification_request_with_a_visible_reason(): void
    {
        $admin = Admin::factory()->create();
        $verificationRequest = DoctorVerificationRequest::factory()->submitted()->create();

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.approvals.update', ['type' => 'doctor-verification', 'id' => $verificationRequest->id]), [
                'decision' => 'reject',
                'rejection_reason' => 'ترخيص مزاولة المهنة غير واضح.',
            ])->assertRedirect(route('admin.approvals.index'));

        $verificationRequest->refresh();
        $this->assertSame(DoctorVerificationRequest::STATUS_REJECTED, $verificationRequest->status);
        $this->assertSame('ترخيص مزاولة المهنة غير واضح.', $verificationRequest->rejection_reason);
        $this->assertSame('rejected', $verificationRequest->doctor->verification_status);
    }

    public function test_rejection_requires_a_reason_and_does_not_change_the_request(): void
    {
        $admin = Admin::factory()->create();
        $credential = DoctorCredential::factory()->create();

        $this->actingAs($admin, 'admin')
            ->from(route('admin.approvals.index'))
            ->patch(route('admin.approvals.update', ['type' => 'credential', 'id' => $credential->id]), [
                'decision' => 'reject',
            ])->assertRedirect(route('admin.approvals.index'))
            ->assertSessionHasErrors('rejection_reason');

        $this->assertSame(DoctorCredential::STATUS_PENDING, $credential->fresh()->verification_status);
    }

    public function test_admin_can_reject_a_credential_with_a_reason_visible_to_the_doctor(): void
    {
        $admin = Admin::factory()->create();
        $credential = DoctorCredential::factory()->create();

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.approvals.update', ['type' => 'credential', 'id' => $credential->id]), [
                'decision' => 'reject',
                'rejection_reason' => '  صورة الشهادة غير واضحة.  ',
            ])->assertRedirect(route('admin.approvals.index'));

        $credential->refresh();
        $this->assertSame(DoctorCredential::STATUS_REJECTED, $credential->verification_status);
        $this->assertSame('صورة الشهادة غير واضحة.', $credential->rejection_reason);
    }

    public function test_admin_can_approve_a_facility_membership_without_publishing_it(): void
    {
        $admin = Admin::factory()->create();
        $membership = DoctorFacility::factory()->create([
            'membership_status' => DoctorFacility::STATUS_PENDING,
            'is_public' => false,
        ]);

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.approvals.update', ['type' => 'facility-membership', 'id' => $membership->id]), [
                'decision' => 'approve',
            ])->assertRedirect(route('admin.approvals.index'));

        $membership->refresh();
        $this->assertSame(DoctorFacility::STATUS_ACTIVE, $membership->membership_status);
        $this->assertFalse($membership->is_public);
    }

    public function test_approving_a_facility_proposal_creates_the_facility_and_active_membership(): void
    {
        $admin = Admin::factory()->create();
        $doctor = Doctor::factory()->create();
        $proposal = FacilityProposal::factory()->for($doctor)->create([
            'name' => 'مركز الشفاء الطبي',
            'city' => 'نابلس',
            'type' => 'medical_center',
            'role' => 'استشاري',
        ]);

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.approvals.update', ['type' => 'facility-proposal', 'id' => $proposal->id]), [
                'decision' => 'approve',
                'status' => FacilityProposal::STATUS_REJECTED,
            ])->assertRedirect(route('admin.approvals.index'));

        $facility = Facility::query()->where('name', 'مركز الشفاء الطبي')->where('city', 'نابلس')->firstOrFail();
        $this->assertSame(FacilityProposal::STATUS_APPROVED, $proposal->fresh()->status);
        $this->assertDatabaseHas('doctor_facilities', [
            'doctor_id' => $doctor->id,
            'facility_id' => $facility->id,
            'role' => 'استشاري',
            'membership_status' => DoctorFacility::STATUS_ACTIVE,
            'is_public' => false,
        ]);
    }

    public function test_processed_request_cannot_be_processed_again(): void
    {
        $admin = Admin::factory()->create();
        $credential = DoctorCredential::factory()->verified()->create();

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.approvals.update', ['type' => 'credential', 'id' => $credential->id]), [
                'decision' => 'reject',
                'rejection_reason' => 'سبب جديد',
            ])->assertConflict();

        $this->assertSame(DoctorCredential::STATUS_VERIFIED, $credential->fresh()->verification_status);
    }
}
