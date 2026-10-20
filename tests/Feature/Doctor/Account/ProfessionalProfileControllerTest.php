<?php

namespace Tests\Feature\Doctor\Account;

use App\Models\Doctor;
use App\Models\DoctorAchievement;
use App\Models\DoctorCredential;
use App\Models\DoctorFacility;
use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ProfessionalProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_doctor_is_redirected_to_login(): void
    {
        $this->get(route('doctor.account.professional-profile'))
            ->assertRedirect(route('doctor.login'));

        $this->put(route('doctor.account.professional-profile.update'))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_authenticated_doctor_sees_their_saved_profile(): void
    {
        $doctor = Doctor::factory()->create([
            'name' => 'د. نور أحمد',
            'languages' => ['العربية', 'الإنجليزية'],
            'professional_title' => 'اختصاصي طب أسرة',
        ]);

        $response = $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.professional-profile'));

        $response->assertOk();
        $response->assertSee('د. نور أحمد');
        $response->assertSee('العربية، الإنجليزية');
        $response->assertSee('اختصاصي طب أسرة');
        $response->assertSee('bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css', escape: false);
        $response->assertSee('class="row g-4"', escape: false);
        $response->assertSee('offcanvas-lg offcanvas-end', escape: false);
        $response->assertSee('data-bs-toggle="offcanvas"', escape: false);
        $response->assertSee('@media (max-width: 991.98px)', escape: false);
        $response->assertSee('professional-profile-page', escape: false);
        $response->assertSee('.professional-profile-page .form-control', escape: false);
        $response->assertSee('class="profile-tabs-list"', escape: false);
        $response->assertSee('grid-template-columns:minmax(0,1.2fr)', escape: false);
        $response->assertSee('select2@4.1.0-rc.0/dist/css/select2.min.css', escape: false);
        $response->assertSee('select2@4.1.0-rc.0/dist/js/select2.min.js', escape: false);
    }

    public function test_all_professional_profile_tabs_are_connected_to_their_panels(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.professional-profile', ['tab' => 'achievements']));

        $response->assertOk();
        foreach (['basic', 'professional', 'specialties', 'facilities', 'credentials', 'achievements', 'preview'] as $tab) {
            $response->assertSee('id="profile-tab-'.$tab.'"', escape: false);
            $response->assertSee('aria-controls="profile-panel-'.$tab.'"', escape: false);
            $response->assertSee('id="profile-panel-'.$tab.'"', escape: false);
            $response->assertSee('aria-labelledby="profile-tab-'.$tab.'"', escape: false);
        }
        $response->assertSee('const requestedTab = "achievements";', escape: false);
        $response->assertSee('history[`${options.historyMode}State`]', escape: false);
        $response->assertSee('class="modal fade profile-modal"', escape: false);
    }

    public function test_authenticated_doctor_can_complete_their_professional_profile(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->put(route('doctor.account.professional-profile.update'), [
                'name' => '  د. نور أحمد  ',
                'languages_text' => 'العربية، الإنجليزية, العربية',
                'bio' => 'نبذة مختصرة عن الطبيب.',
                'professional_title' => '  اختصاصي طب أسرة ',
                'years_experience' => 8,
                'professional_bio' => 'خبرة مهنية في الرعاية الصحية الأولية.',
                'verification_status' => 'verified',
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile'));
        $response->assertSessionHas('status', 'تم حفظ الملف المهني بنجاح.');

        $doctor->refresh();

        $this->assertSame('د. نور أحمد', $doctor->name);
        $this->assertSame(['العربية', 'الإنجليزية'], $doctor->languages);
        $this->assertSame('اختصاصي طب أسرة', $doctor->professional_title);
        $this->assertSame(8, $doctor->years_experience);
        $this->assertSame('profile_incomplete', $doctor->verification_status);
        $this->assertNotNull($doctor->profile_completed_at);
    }

    public function test_completed_profile_can_continue_to_professional_verification(): void
    {
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->put(route('doctor.account.professional-profile.update'), [
                'name' => 'د. نور أحمد',
                'professional_title' => 'اختصاصي طب أسرة',
                'years_experience' => 8,
                'professional_bio' => 'خبرة مهنية في الرعاية الصحية الأولية.',
                'onboarding' => 1,
                'next' => 1,
            ]);

        $response->assertRedirect(route('doctor.account.verification', ['onboarding' => 1]));
        $response->assertSessionHas('status', 'تم حفظ الملف المهني. أكمل الآن بيانات التحقق المهني.');
        $this->assertNotNull($doctor->fresh()->profile_completed_at);
    }

    public function test_required_professional_fields_are_validated_in_arabic(): void
    {
        $doctor = Doctor::factory()->create([
            'professional_title' => null,
            'years_experience' => null,
            'professional_bio' => null,
        ]);

        $response = $this->actingAs($doctor, 'doctor')
            ->from(route('doctor.account.professional-profile'))
            ->put(route('doctor.account.professional-profile.update'), [
                'name' => '',
                'professional_title' => '',
                'years_experience' => '',
                'professional_bio' => '',
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile'));
        $response->assertInvalid([
            'name' => 'أدخل الاسم الكامل.',
            'professional_title' => 'أدخل المسمى المهني.',
            'years_experience' => 'أدخل عدد سنوات الخبرة.',
            'professional_bio' => 'أدخل النبذة المهنية.',
        ]);

        $this->assertNull($doctor->fresh()->profile_completed_at);
    }

    public function test_public_preview_uses_real_doctor_data_and_filters_private_or_unverified_items(): void
    {
        $doctor = Doctor::factory()->create([
            'name' => 'د. ليان الخطيب',
            'bio' => 'نبذة حقيقية تظهر في الملف العام.',
            'professional_title' => 'اختصاصية طب الأسرة',
            'years_experience' => 9,
            'languages' => ['العربية', 'الإنجليزية'],
            'verification_status' => 'verified',
        ]);

        DoctorCredential::factory()->verified()->for($doctor)->create(['title' => 'المؤهل الموثق']);
        DoctorCredential::factory()->for($doctor)->create(['title' => 'المؤهل قيد المراجعة']);
        DoctorAchievement::factory()->verified()->for($doctor)->create(['title' => 'الإنجاز الموثق']);
        DoctorAchievement::factory()->for($doctor)->create(['title' => 'الإنجاز قيد المراجعة']);

        $publicFacility = Facility::factory()->create(['name' => 'المنشأة العامة']);
        $privateFacility = Facility::factory()->create(['name' => 'المنشأة المخفية']);
        DoctorFacility::factory()->for($doctor)->for($publicFacility)->create(['is_public' => true]);
        DoctorFacility::factory()->for($doctor)->for($privateFacility)->create(['is_public' => false]);

        $response = $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.professional-profile', ['tab' => 'preview']));

        $response->assertOk();
        $response->assertSee('د. ليان الخطيب');
        $response->assertSee('نبذة حقيقية تظهر في الملف العام.');
        $response->assertSee('المؤهل الموثق');
        $response->assertSee('الإنجاز الموثق');
        $response->assertSee('المنشأة العامة');
        $response->assertDontSee('د. محمد أحمد');
        $response->assertDontSee('preview.innerHTML', escape: false);
        $response->assertViewHas('verifiedCredentials', fn (Collection $credentials): bool => $credentials->pluck('title')->all() === ['المؤهل الموثق']);
        $response->assertViewHas('verifiedAchievements', fn (Collection $achievements): bool => $achievements->pluck('title')->all() === ['الإنجاز الموثق']);
        $response->assertViewHas('publicFacilityMemberships', fn (Collection $memberships): bool => $memberships->pluck('facility.name')->all() === ['المنشأة العامة']);
    }
}
