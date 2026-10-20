<?php

namespace Tests\Feature\Doctor\Account;

use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use App\Models\Specialty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorSpecialtyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_doctor_is_redirected_to_login(): void
    {
        $specialty = Specialty::factory()->create();

        $this->post(route('doctor.account.professional-profile.specialties.store'), [
            'specialty_id' => $specialty->id,
            'type' => DoctorSpecialty::TYPE_PRIMARY,
        ])->assertRedirect(route('doctor.login'));
    }

    public function test_doctor_can_add_a_primary_specialty_for_review(): void
    {
        $doctor = Doctor::factory()->create();
        $specialty = Specialty::factory()->create(['name_ar' => 'طب الأسرة']);

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.specialties.store'), [
                'specialty_id' => $specialty->id,
                'type' => DoctorSpecialty::TYPE_PRIMARY,
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'specialties']));
        $response->assertSessionHas('status', 'تم حفظ التخصص الرئيسي وإرساله للمراجعة.');

        $this->assertDatabaseHas('doctor_specialties', [
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'type' => DoctorSpecialty::TYPE_PRIMARY,
            'verification_status' => DoctorSpecialty::STATUS_PENDING,
        ]);
    }

    public function test_profile_is_only_complete_after_adding_a_primary_specialty(): void
    {
        $doctor = Doctor::factory()->create([
            'professional_title' => 'اختصاصي طب أسرة',
            'years_experience' => 8,
            'professional_bio' => 'خبرة مهنية في الرعاية الصحية الأولية.',
        ]);

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.professional-profile'))
            ->assertOk()
            ->assertSee('id="profile-completion-value">80%', escape: false);

        $specialty = Specialty::factory()->create(['name_ar' => 'طب الأسرة']);
        DoctorSpecialty::factory()->for($doctor)->for($specialty)->create();

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.professional-profile'))
            ->assertOk()
            ->assertSee('id="profile-completion-value">100%', escape: false)
            ->assertSee('طب الأسرة')
            ->assertSee('data-current-value="'.$specialty->id.'"', escape: false)
            ->assertSee('<option value="'.$specialty->id.'" selected>طب الأسرة</option>', escape: false);
    }

    public function test_child_specialty_cannot_be_saved_as_the_primary_specialty(): void
    {
        $doctor = Doctor::factory()->create();
        $parent = Specialty::factory()->create();
        $child = Specialty::factory()->forParent($parent)->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->from(route('doctor.account.professional-profile', ['tab' => 'specialties']))
            ->post(route('doctor.account.professional-profile.specialties.store'), [
                'specialty_id' => $child->id,
                'type' => DoctorSpecialty::TYPE_PRIMARY,
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'specialties']));
        $response->assertInvalid(['specialty_id' => 'اختر تخصصًا رئيسيًا من القائمة.']);
        $this->assertDatabaseEmpty('doctor_specialties');
    }

    public function test_doctor_can_add_only_a_subspecialty_of_their_primary_specialty(): void
    {
        $doctor = Doctor::factory()->create();
        $primary = Specialty::factory()->create();
        $allowedSubspecialty = Specialty::factory()->forParent($primary)->create();
        $otherPrimary = Specialty::factory()->create();
        $unrelatedSubspecialty = Specialty::factory()->forParent($otherPrimary)->create();
        DoctorSpecialty::factory()->for($doctor)->for($primary)->create();

        $this->actingAs($doctor, 'doctor')
            ->from(route('doctor.account.professional-profile', ['tab' => 'specialties']))
            ->post(route('doctor.account.professional-profile.specialties.store'), [
                'specialty_id' => $unrelatedSubspecialty->id,
                'type' => DoctorSpecialty::TYPE_SUBSPECIALTY,
            ])
            ->assertInvalid(['specialty_id' => 'اختر تخصصًا فرعيًا تابعًا لتخصصك الرئيسي.']);

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.specialties.store'), [
                'specialty_id' => $allowedSubspecialty->id,
                'type' => DoctorSpecialty::TYPE_SUBSPECIALTY,
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'specialties']));
        $this->assertDatabaseHas('doctor_specialties', [
            'doctor_id' => $doctor->id,
            'specialty_id' => $allowedSubspecialty->id,
            'type' => DoctorSpecialty::TYPE_SUBSPECIALTY,
        ]);
        $this->assertDatabaseMissing('doctor_specialties', [
            'doctor_id' => $doctor->id,
            'specialty_id' => $unrelatedSubspecialty->id,
        ]);
    }

    public function test_doctor_can_delete_their_pending_subspecialty(): void
    {
        $doctor = Doctor::factory()->create();
        $assignment = DoctorSpecialty::factory()->subspecialty()->for($doctor)->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.specialties.destroy', $assignment));

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'specialties']));
        $this->assertModelMissing($assignment);
    }

    public function test_doctor_cannot_delete_another_doctors_specialty(): void
    {
        $doctor = Doctor::factory()->create();
        $otherDoctor = Doctor::factory()->create();
        $assignment = DoctorSpecialty::factory()->subspecialty()->for($otherDoctor)->create();

        $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.specialties.destroy', $assignment))
            ->assertNotFound();

        $this->assertModelExists($assignment);
    }

    public function test_verified_specialty_cannot_be_deleted(): void
    {
        $doctor = Doctor::factory()->create();
        $assignment = DoctorSpecialty::factory()->subspecialty()->verified()->for($doctor)->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.specialties.destroy', $assignment));

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'specialties']));
        $response->assertInvalid(['specialty_id' => 'لا يمكن حذف تخصص موثّق. تواصل مع الدعم لتعديله.']);
        $this->assertModelExists($assignment);
    }
}
