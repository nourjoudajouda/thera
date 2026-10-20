<?php

namespace Tests\Feature\Doctor\Account;

use App\Models\Doctor;
use App\Models\Facility;
use App\Models\FacilityProposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorFacilityProposalControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_doctor_cannot_submit_a_facility_proposal(): void
    {
        $this->post(route('doctor.account.professional-profile.facility-proposals.store'))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_doctor_can_submit_a_new_facility_proposal_for_review(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.facility-proposals.store'), [
                'name' => '  مركز الحياة الطبي  ',
                'type' => 'medical_center',
                'city' => '  نابلس  ',
                'role' => '  طبيب  ',
                'status' => FacilityProposal::STATUS_APPROVED,
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'facilities']));
        $response->assertSessionHas('status', 'تم إرسال طلب إضافة المنشأة للمراجعة.');
        $this->assertDatabaseHas('facility_proposals', [
            'doctor_id' => $doctor->id,
            'name' => 'مركز الحياة الطبي',
            'type' => 'medical_center',
            'city' => 'نابلس',
            'role' => 'طبيب',
            'status' => FacilityProposal::STATUS_PENDING,
        ]);
    }

    public function test_existing_facility_cannot_be_submitted_as_a_new_proposal(): void
    {
        $doctor = Doctor::factory()->create();
        Facility::factory()->create(['name' => 'مركز الحياة الطبي', 'city' => 'نابلس']);

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.facility-proposals.store'), [
                'name' => 'مركز الحياة الطبي',
                'type' => 'medical_center',
                'city' => 'نابلس',
                'role' => 'طبيب',
            ]);

        $response->assertInvalid([
            'name' => 'هذه المنشأة موجودة بالفعل؛ استخدم طلب الانضمام إليها.',
        ], 'facilityProposal');
        $this->assertDatabaseEmpty('facility_proposals');
    }

    public function test_doctor_can_cancel_their_pending_facility_proposal(): void
    {
        $doctor = Doctor::factory()->create();
        $proposal = FacilityProposal::factory()->for($doctor)->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.facility-proposals.destroy', $proposal));

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'facilities']));
        $response->assertSessionHas('status', 'تم إلغاء طلب إضافة المنشأة.');
        $this->assertModelMissing($proposal);
    }

    public function test_doctor_cannot_submit_the_same_pending_facility_proposal_twice(): void
    {
        $doctor = Doctor::factory()->create();
        FacilityProposal::factory()->for($doctor)->create([
            'name' => 'مركز الحياة الطبي',
            'city' => 'نابلس',
        ]);

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.facility-proposals.store'), [
                'name' => 'مركز الحياة الطبي',
                'type' => 'medical_center',
                'city' => 'نابلس',
                'role' => 'طبيب',
            ]);

        $response->assertInvalid([
            'name' => 'سبق أن أرسلت طلب إضافة لهذه المنشأة وهو قيد المراجعة.',
        ], 'facilityProposal');
        $this->assertSame(1, $doctor->facilityProposals()->count());
    }

    public function test_doctor_cannot_cancel_another_doctors_facility_proposal(): void
    {
        $doctor = Doctor::factory()->create();
        $proposal = FacilityProposal::factory()->create();

        $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.facility-proposals.destroy', $proposal))
            ->assertNotFound();

        $this->assertModelExists($proposal);
    }
}
