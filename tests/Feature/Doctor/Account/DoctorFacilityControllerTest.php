<?php

namespace Tests\Feature\Doctor\Account;

use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorFacilityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_doctor_is_redirected_to_login(): void
    {
        $membership = DoctorFacility::factory()->create();

        $this->patch(route('doctor.account.professional-profile.facilities.update', $membership), [
            'is_public' => false,
        ])->assertRedirect(route('doctor.login'));

        $this->post(route('doctor.account.professional-profile.facilities.store'))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_doctor_can_request_membership_in_an_active_facility(): void
    {
        $doctor = Doctor::factory()->create();
        $facility = Facility::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.facilities.store'), [
                'facility_id' => $facility->id,
                'role' => '  استشاري طب أسرة  ',
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'facilities']));
        $response->assertSessionHas('status', 'تم إرسال طلب الانضمام إلى المنشأة للمراجعة.');
        $this->assertDatabaseHas('doctor_facilities', [
            'doctor_id' => $doctor->id,
            'facility_id' => $facility->id,
            'role' => 'استشاري طب أسرة',
            'membership_status' => DoctorFacility::STATUS_PENDING,
            'is_public' => false,
        ]);
    }

    public function test_doctor_cannot_request_the_same_facility_twice(): void
    {
        $doctor = Doctor::factory()->create();
        $membership = DoctorFacility::factory()->for($doctor)->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.facilities.store'), [
                'facility_id' => $membership->facility_id,
                'role' => 'طبيب',
            ]);

        $response->assertInvalid([
            'facility_id' => 'لديك عضوية أو طلب سابق مرتبط بهذه المنشأة.',
        ], 'facilityMembership');
        $this->assertSame(1, $doctor->facilityMemberships()->count());
    }

    public function test_doctor_cannot_request_membership_in_an_inactive_facility(): void
    {
        $doctor = Doctor::factory()->create();
        $facility = Facility::factory()->create(['is_active' => false]);

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.facilities.store'), [
                'facility_id' => $facility->id,
                'role' => 'طبيب',
            ]);

        $response->assertInvalid([
            'facility_id' => 'المنشأة المحددة غير متاحة.',
        ], 'facilityMembership');
        $this->assertDatabaseEmpty('doctor_facilities');
    }

    public function test_doctor_can_change_the_public_visibility_of_their_facility(): void
    {
        $doctor = Doctor::factory()->create();
        $membership = DoctorFacility::factory()->for($doctor)->create(['is_public' => true]);

        $response = $this->actingAs($doctor, 'doctor')
            ->patch(route('doctor.account.professional-profile.facilities.update', $membership), [
                'is_public' => false,
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'facilities']));
        $response->assertSessionHas('status', 'تم تحديث ظهور المنشأة في الملف العام.');
        $this->assertFalse($membership->fresh()->is_public);
    }

    public function test_doctor_cannot_change_another_doctors_facility_membership(): void
    {
        $doctor = Doctor::factory()->create();
        $membership = DoctorFacility::factory()->create(['is_public' => true]);

        $this->actingAs($doctor, 'doctor')
            ->patch(route('doctor.account.professional-profile.facilities.update', $membership), [
                'is_public' => false,
            ])
            ->assertNotFound();

        $this->assertTrue($membership->fresh()->is_public);
    }

    public function test_doctor_cannot_publish_a_pending_facility_membership(): void
    {
        $doctor = Doctor::factory()->create();
        $membership = DoctorFacility::factory()->for($doctor)->create([
            'membership_status' => DoctorFacility::STATUS_PENDING,
            'is_public' => false,
        ]);

        $response = $this->actingAs($doctor, 'doctor')
            ->patch(route('doctor.account.professional-profile.facilities.update', $membership), [
                'is_public' => true,
            ]);

        $response->assertInvalid([
            'facility_id' => 'يمكن تغيير الظهور العام للعضويات النشطة فقط.',
        ], 'facilityMembership');
        $this->assertFalse($membership->fresh()->is_public);
    }

    public function test_doctor_can_cancel_their_pending_facility_request(): void
    {
        $doctor = Doctor::factory()->create();
        $membership = DoctorFacility::factory()->for($doctor)->create([
            'membership_status' => DoctorFacility::STATUS_PENDING,
        ]);

        $response = $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.facilities.destroy', $membership));

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'facilities']));
        $response->assertSessionHas('status', 'تم إلغاء طلب الانضمام إلى المنشأة.');
        $this->assertModelMissing($membership);
    }
}
