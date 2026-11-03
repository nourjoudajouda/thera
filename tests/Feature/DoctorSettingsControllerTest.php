<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_summary_has_template_cards_owned_services_and_native_icon_links_to_styled_destinations(): void
    {
        $membership = DoctorFacility::factory()->create();
        $membership->facility->update(['is_active' => true]);
        $service = DoctorService::factory()->create(['doctor_facility_id' => $membership->id, 'name' => 'متابعة طب الأسرة', 'is_active' => true]);
        DoctorService::factory()->create(['doctor_facility_id' => $membership->id, 'name' => 'خدمة متوقفة', 'is_active' => false]);
        DoctorService::factory()->create(['name' => 'خدمة طبيب آخر']);
        $doctor = $membership->doctor;
        $doctor->update(['verification_status' => 'verified']);
        $this->actingAs($doctor, 'doctor');

        $response = $this->get(route('doctor.account.settings', ['tab' => 'profile']))->assertOk()
            ->assertSee($service->name)->assertDontSee('خدمة متوقفة')->assertDontSee('خدمة طبيب آخر')->assertDontSee('فتح الصفحة');
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertSame(4, $xpath->query('//*[@id="section-profile"]//dl/div')->length);
        $links = $xpath->query('//*[@id="section-profile"]//a[contains(@class,"settings-open-page")]');
        $this->assertSame(3, $links->length);
        foreach ($links as $link) {
            $this->assertSame('_self', $link->getAttribute('target'));
            $this->assertNotEmpty($link->getAttribute('aria-label'));
            $this->assertSame('open_in_new', trim($link->textContent));
            $target = $this->get($link->getAttribute('href'))->assertOk()->assertSee('id="app-sidebar"', false);
            $target->assertSee(str_contains($link->getAttribute('href'), 'verification') ? 'التحقق' : 'css/doctor-professional-profile.css', false);
            $target->assertDontSee('css/doctor-settings.css', false);
        }
    }

    public function test_settings_require_a_doctor_session(): void
    {
        $this->get(route('doctor.account.settings'))->assertRedirect(route('doctor.login'));
        $this->putJson(route('doctor.account.settings.update'), $this->payload())->assertUnauthorized();
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.account.settings'))->assertRedirect(route('doctor.login'));
    }

    public function test_settings_show_real_account_data_without_persisting_on_read(): void
    {
        $doctor = Doctor::factory()->create(['name' => 'د. سارة خليل', 'verification_status' => 'profile_incomplete']);
        $this->actingAs($doctor, 'doctor')->get(route('doctor.account.settings', ['tab' => 'profile']))->assertOk()
            ->assertSee('د. سارة خليل')->assertSee($doctor->email)->assertSee('لم يُعتمد التحقق المهني بعد')
            ->assertDontSee('د. محمد أحمد')->assertDontSee('localStorage', false)
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->assertNull($doctor->fresh()->account_preferences);
        $this->assertDatabaseCount('doctor_setting_events', 0);
    }

    public function test_ajax_save_persists_only_own_preferences_with_audit_and_survives_a_fresh_request(): void
    {
        $membership = DoctorFacility::factory()->create();
        $other = Doctor::factory()->create();
        $data = $this->payload() + ['doctor_id' => $other->id, 'verification_status' => 'verified', 'email' => 'forged@example.test'];
        $data['preferred_membership_id'] = $membership->id;
        $data['notifications']['appointments'] = '1';
        $doctor = $membership->doctor;
        $before = $doctor->getAttributes();
        $this->actingAs($doctor, 'doctor')->putJson(route('doctor.account.settings.update'), $data)->assertOk()->assertJsonPath('url', route('doctor.account.settings', ['tab' => 'general']));
        $saved = $doctor->fresh();
        $this->assertSame('busy', $saved->accountPreferences()['availability']);
        $this->assertSame($membership->id, $saved->accountPreferences()['preferred_membership_id']);
        $this->assertTrue($saved->accountPreferences()['notifications']['appointments']);
        $this->assertSame($before['email'], $saved->email);
        $this->assertSame($before['verification_status'], $saved->verification_status);
        $this->assertNull($other->fresh()->account_preferences);
        $this->assertDatabaseHas('doctor_setting_events', ['doctor_id' => $doctor->id, 'revision' => 1]);
        $this->get(route('doctor.account.settings'))->assertOk()->assertViewHas('preferences', fn ($prefs) => $prefs['availability'] === 'busy' && $prefs['notifications']['appointments'] === true);
    }

    public function test_retry_does_not_duplicate_audit_and_stale_changed_save_does_not_overwrite(): void
    {
        $doctor = Doctor::factory()->create();
        $this->actingAs($doctor, 'doctor');
        $data = $this->payload();
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $data['availability'] = 'available';
        $this->putJson(route('doctor.account.settings.update'), $data)->assertUnprocessable()->assertJsonValidationErrors('revision');
        $this->assertSame('busy', $doctor->fresh()->accountPreferences()['availability']);
        $this->assertDatabaseCount('doctor_setting_events', 1);
    }

    #[TestWith(['foreign'])]
    #[TestWith(['suspended'])]
    #[TestWith(['inactive'])]
    public function test_membership_must_be_owned_and_active_with_an_active_facility(string $case): void
    {
        $doctor = Doctor::factory()->create();
        $membership = DoctorFacility::factory()->create(['doctor_id' => $case === 'foreign' ? Doctor::factory()->create()->id : $doctor->id, 'membership_status' => $case === 'suspended' ? 'suspended' : 'active']);
        if ($case === 'inactive') {
            $membership->facility->update(['is_active' => false]);
        }
        $this->actingAs($doctor, 'doctor')->putJson(route('doctor.account.settings.update'), array_replace($this->payload(), ['preferred_membership_id' => $membership->id]))->assertUnprocessable()->assertJsonValidationErrors('preferred_membership_id');
        $this->assertNull($doctor->fresh()->account_preferences);
        $this->assertDatabaseCount('doctor_setting_events', 0);
    }

    #[TestWith(['availability', 'invented'])]
    #[TestWith(['tab', 'invented'])]
    #[TestWith(['revision', -1])]
    #[TestWith(['notifications.appointments', 'invalid'])]
    #[TestWith(['notifications.extra', true])]
    #[TestWith(['language', 'en'])]
    #[TestWith(['payment_policy', 'after_visit'])]
    public function test_invalid_or_unsupported_fields_cannot_be_saved(string $key, mixed $value): void
    {
        $doctor = Doctor::factory()->create();
        $data = $this->payload();
        data_set($data, $key, $value);
        $this->actingAs($doctor, 'doctor')->putJson(route('doctor.account.settings.update'), $data)->assertUnprocessable();
        $this->assertNull($doctor->fresh()->account_preferences);
    }

    public function test_availability_does_not_change_schedule_booking_finance_or_professional_verification(): void
    {
        $schedule = DoctorSchedule::factory()->create();
        $doctor = $schedule->doctorFacility->doctor;
        $before = $schedule->fresh()->getAttributes();
        $this->actingAs($doctor, 'doctor')->putJson(route('doctor.account.settings.update'), $this->payload())->assertOk();
        $this->assertSame($before, $schedule->fresh()->getAttributes());
        $this->actingAs($doctor->fresh(), 'doctor')->get(route('doctor.dashboard'))->assertOk()->assertSee('مشغول مؤقتًا');
        $this->assertDatabaseCount('doctor_payments', 0);
        $this->assertDatabaseCount('doctor_appointments', 0);
        $this->assertSame($doctor->verification_status, $doctor->fresh()->verification_status);
    }

    public function test_html_save_redirects_and_tampered_tab_is_rejected(): void
    {
        $doctor = Doctor::factory()->create();
        $this->actingAs($doctor, 'doctor')->put(route('doctor.account.settings.update'), $this->payload())->assertRedirect(route('doctor.account.settings', ['tab' => 'general']));
        $this->getJson(route('doctor.account.settings', ['tab' => 'invalid']))->assertUnprocessable();
    }

    #[TestWith(['none', 'after_visit', false])]
    #[TestWith(['percentage', 'before_visit', true])]
    #[TestWith(['percentage', 'at_arrival', true])]
    #[TestWith(['percentage', 'after_visit', false])]
    #[TestWith(['full', 'after_visit', true])]
    public function test_doctor_saves_payment_policy_and_custom_percentage_without_charging_anyone(string $type, string $timing, bool $requiresCollection): void
    {
        $doctor = Doctor::factory()->create();
        $other = Doctor::factory()->create();
        $data = $this->payload();
        $data['tab'] = 'payment';
        $data['payment_policy'] = ['type' => $type, 'deposit_percent' => 35, 'remaining_timing' => $timing, 'allow_early_payment' => false];
        $data['doctor_id'] = $other->id;

        $this->actingAs($doctor, 'doctor')->putJson(route('doctor.account.settings.update'), $data)->assertOk();

        $this->assertSame($data['payment_policy'], $doctor->fresh()->accountPreferences()['payment_policy']);
        $this->assertSame($requiresCollection, $doctor->fresh()->requiresCollectionBeforeVisit());
        $this->assertNull($other->fresh()->account_preferences);
        $this->assertDatabaseHas('doctor_setting_events', ['doctor_id' => $doctor->id, 'revision' => 1]);
        $this->get(route('doctor.account.settings', ['tab' => 'payment']))->assertOk()->assertViewHas('preferences', fn ($preferences) => $preferences['payment_policy']['deposit_percent'] === 35 && $preferences['payment_policy']['type'] === $type);
        $this->assertDatabaseEmpty('doctor_payments');
        $this->assertDatabaseEmpty('doctor_appointments');
    }

    #[TestWith(['payment_policy.type', 'invalid', 'payment_policy.type'])]
    #[TestWith(['payment_policy.deposit_percent', 0, 'payment_policy.deposit_percent'])]
    #[TestWith(['payment_policy.deposit_percent', 100, 'payment_policy.deposit_percent'])]
    #[TestWith(['payment_policy.deposit_percent', 15.5, 'payment_policy.deposit_percent'])]
    #[TestWith(['payment_policy.deposit_percent', null, 'payment_policy.deposit_percent'])]
    #[TestWith(['payment_policy.remaining_timing', 'invalid', 'payment_policy.remaining_timing'])]
    #[TestWith(['payment_policy.allow_early_payment', 'invalid', 'payment_policy.allow_early_payment'])]
    #[TestWith(['payment_policy.extra', 'forged', 'payment_policy'])]
    public function test_invalid_payment_policy_does_not_change_preferences(string $key, mixed $value, string $error): void
    {
        $doctor = Doctor::factory()->create();
        $data = $this->payload();
        $data['payment_policy'] = ['type' => 'percentage', 'deposit_percent' => 30, 'remaining_timing' => 'before_visit', 'allow_early_payment' => true];
        data_set($data, $key, $value);

        $this->actingAs($doctor, 'doctor')->putJson(route('doctor.account.settings.update'), $data)->assertUnprocessable()->assertJsonValidationErrors($error);

        $this->assertNull($doctor->fresh()->account_preferences);
        $this->assertDatabaseEmpty('doctor_setting_events');
    }

    public function test_full_policy_preserves_disabled_preferences_and_general_saves_do_not_reset_percentage(): void
    {
        $doctor = Doctor::factory()->create();
        $doctor->account_preferences = ['payment_policy' => ['type' => 'percentage', 'deposit_percent' => 45, 'remaining_timing' => 'after_visit', 'allow_early_payment' => true]];
        $doctor->save();
        $data = $this->payload();
        $data['payment_policy'] = ['type' => 'full'];

        $this->actingAs($doctor, 'doctor')->putJson(route('doctor.account.settings.update'), $data)->assertOk();

        $this->assertSame(['type' => 'full', 'deposit_percent' => 45, 'remaining_timing' => 'after_visit', 'allow_early_payment' => true], $doctor->fresh()->accountPreferences()['payment_policy']);
        $data = $this->payload();
        $data['revision'] = 1;
        $data['availability'] = 'available';
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $this->assertSame(45, $doctor->fresh()->accountPreferences()['payment_policy']['deposit_percent']);
        $this->assertTrue($doctor->fresh()->requiresCollectionBeforeVisit());
    }

    public function test_retried_policy_save_is_idempotent_and_stale_percentage_does_not_overwrite(): void
    {
        $doctor = Doctor::factory()->create();
        $data = $this->payload();
        $data['payment_policy'] = ['type' => 'percentage', 'deposit_percent' => 30, 'remaining_timing' => 'before_visit', 'allow_early_payment' => true];
        $this->actingAs($doctor, 'doctor');

        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $data['payment_policy']['deposit_percent'] = 50;
        $this->putJson(route('doctor.account.settings.update'), $data)->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertSame(30, $doctor->fresh()->accountPreferences()['payment_policy']['deposit_percent']);
        $this->assertDatabaseCount('doctor_setting_events', 1);
    }

    /** @return array<string,mixed> */
    private function payload(): array
    {
        return ['revision' => 0, 'tab' => 'general', 'preferred_membership_id' => null, 'availability' => 'busy', 'notifications' => array_fill_keys(array_keys(Doctor::NOTIFICATION_PREFERENCES), false)];
    }
}
