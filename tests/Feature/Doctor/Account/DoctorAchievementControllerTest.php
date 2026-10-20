<?php

namespace Tests\Feature\Doctor\Account;

use App\Models\Doctor;
use App\Models\DoctorAchievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DoctorAchievementControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_doctor_is_redirected_to_login(): void
    {
        $this->post(route('doctor.account.professional-profile.achievements.store'))
            ->assertRedirect(route('doctor.login'));
    }

    public function test_doctor_can_add_an_achievement_with_private_evidence(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->post(route('doctor.account.professional-profile.achievements.store'), [
                'type' => 'award',
                'title' => 'جائزة التميز في الرعاية الوقائية',
                'organization' => 'الملتقى الطبي الإقليمي',
                'year' => 2025,
                'description' => 'تكريم مهني مرتبط بتحسين متابعة المرضى.',
                'evidence' => UploadedFile::fake()->image('award.jpg'),
            ]);

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'achievements']));
        $response->assertSessionHas('status', 'تمت إضافة الإنجاز وإرساله للمراجعة.');

        $achievement = DoctorAchievement::query()->sole();
        $this->assertSame($doctor->id, $achievement->doctor_id);
        $this->assertSame(DoctorAchievement::STATUS_PENDING, $achievement->verification_status);
        $this->assertSame('award.jpg', $achievement->evidence_original_name);
        Storage::disk('local')->assertExists($achievement->evidence_path);
    }

    public function test_achievement_rejects_future_year_and_unsafe_evidence(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();

        $response = $this->actingAs($doctor, 'doctor')
            ->from(route('doctor.account.professional-profile'))
            ->post(route('doctor.account.professional-profile.achievements.store'), [
                'type' => 'award',
                'title' => 'إنجاز مهني',
                'organization' => 'جهة طبية',
                'year' => now()->year + 1,
                'description' => 'وصف الإنجاز المهني.',
                'evidence' => UploadedFile::fake()->create('script.php', 10, 'text/x-php'),
            ]);

        $response->assertInvalid([
            'year' => 'سنة الإنجاز لا يمكن أن تكون في المستقبل.',
            'evidence' => 'يجب أن يكون ملف الإثبات PDF أو JPG أو PNG.',
        ], 'achievement');
        $this->assertDatabaseEmpty('doctor_achievements');
    }

    public function test_doctor_cannot_download_another_doctors_achievement_evidence(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $achievement = DoctorAchievement::factory()->create();
        Storage::disk('local')->put($achievement->evidence_path, 'private evidence');

        $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.professional-profile.achievements.evidence', $achievement))
            ->assertNotFound();
    }

    public function test_doctor_can_delete_a_pending_achievement_and_its_evidence(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $achievement = DoctorAchievement::factory()->for($doctor)->create();
        Storage::disk('local')->put($achievement->evidence_path, 'private evidence');

        $response = $this->actingAs($doctor, 'doctor')
            ->delete(route('doctor.account.professional-profile.achievements.destroy', $achievement));

        $response->assertRedirect(route('doctor.account.professional-profile', ['tab' => 'achievements']));
        $this->assertModelMissing($achievement);
        Storage::disk('local')->assertMissing($achievement->evidence_path);
    }

    public function test_profile_escapes_achievement_content(): void
    {
        $doctor = Doctor::factory()->create();
        DoctorAchievement::factory()->for($doctor)->create([
            'title' => '<script>alert("xss")</script>',
            'description' => '<img src=x onerror=alert(1)>',
        ]);

        $response = $this->actingAs($doctor, 'doctor')
            ->get(route('doctor.account.professional-profile', ['tab' => 'achievements']));

        $response->assertOk();
        $response->assertSee('&lt;script&gt;', escape: false);
        $response->assertDontSee('<script>alert("xss")</script>', escape: false);
        $response->assertDontSee('<img src=x onerror=alert(1)>', escape: false);
    }
}
