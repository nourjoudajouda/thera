<?php

namespace Tests\Feature\Doctor\Practice;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleException;
use App\Models\DoctorService;
use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admins_cannot_access_doctor_service_management(): void
    {
        $this->get(route('doctor.practice.services'))->assertRedirect(route('doctor.login'));
        $this->post(route('doctor.practice.services.store'))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.practice.services'))->assertRedirect(route('doctor.login'));
    }

    public function test_services_are_isolated_by_doctor_and_render_with_responsive_controls(): void
    {
        $membership = $this->membership();
        DoctorService::factory()->for($membership, 'doctorFacility')->create(['name' => 'خدمة طبيبي']);
        DoctorService::factory()->create(['name' => 'خدمة طبيب آخر']);
        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.services'))->assertOk()
            ->assertSee('خدمة طبيبي')->assertDontSee('خدمة طبيب آخر')
            ->assertSee('id="service-drawer"', false)->assertSee('d-lg-none', false)->assertSee('select2@4.1.0', false);
    }

    #[TestWith(['JOD', '30.125', 30125])]
    #[TestWith(['ILS', '30.12', 3012])]
    #[TestWith(['USD', '0.10', 10])]
    public function test_price_is_saved_exactly_in_minor_currency_units(string $currency, string $price, int $minor): void
    {
        $membership = $this->membership();
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.services.store'), $this->payload($membership, [
            'name' => '  استشارة أولية  ', 'currency' => $currency, 'price' => $price,
            'doctor_id' => 999, 'price_minor' => 1, 'deposit_percentage' => 90,
        ]))->assertRedirect(route('doctor.practice.services'))->assertSessionHasNoErrors();
        $service = DoctorService::query()->firstOrFail();
        $this->assertSame($minor, $service->price_minor);
        $this->assertSame($membership->id, $service->doctor_facility_id);
        $this->assertSame('استشارة أولية', $service->name);
        $this->assertFalse($service->is_active);
    }

    #[TestWith(['pending'])]
    #[TestWith(['suspended'])]
    #[TestWith(['rejected'])]
    public function test_service_cannot_be_added_to_a_nonactive_membership(string $status): void
    {
        $membership = $this->membership();
        $membership->update(['membership_status' => $status]);
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.services.store'), $this->payload($membership))
            ->assertSessionHasErrors('doctor_facility_id');
        $this->assertDatabaseEmpty('doctor_services');
    }

    public function test_service_cannot_be_added_to_another_doctors_membership(): void
    {
        $own = $this->membership();
        $other = $this->membership();
        $this->actingAs($own->doctor, 'doctor')->post(route('doctor.practice.services.store'), $this->payload($other))
            ->assertSessionHasErrors('doctor_facility_id');
        $this->assertDatabaseEmpty('doctor_services');
    }

    public function test_inactive_facility_cannot_receive_new_services(): void
    {
        $membership = $this->membership();
        $membership->facility->update(['is_active' => false]);
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.services.store'), $this->payload($membership))
            ->assertSessionHasErrors('doctor_facility_id');
        $this->assertDatabaseEmpty('doctor_services');
    }

    public function test_duplicate_name_in_same_membership_is_rejected(): void
    {
        $membership = $this->membership();
        DoctorService::factory()->for($membership, 'doctorFacility')->create(['name' => 'استشارة']);
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.services.store'), $this->payload($membership))
            ->assertSessionHasErrors('name');
        $this->assertDatabaseCount('doctor_services', 1);
    }

    #[TestWith(['price', '-1'])]
    #[TestWith(['price', '1e3'])]
    #[TestWith(['price', '12.1234'])]
    #[TestWith(['currency', 'FAKE'])]
    #[TestWith(['duration_minutes', 0])]
    #[TestWith(['duration_minutes', 30.5])]
    #[TestWith(['duration_minutes', 481])]
    public function test_invalid_service_values_are_not_saved(string $field, mixed $value): void
    {
        $membership = $this->membership();
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.services.store'),
            $this->payload($membership, [$field => $value]))->assertSessionHasErrors($field);
        $this->assertDatabaseEmpty('doctor_services');
    }

    public function test_verified_doctor_can_update_activate_and_pause_a_service(): void
    {
        $membership = $this->membership();
        $service = DoctorService::factory()->for($membership, 'doctorFacility')->create(['name' => 'استشارة']);
        $this->actingAs($membership->doctor, 'doctor')->put(route('doctor.practice.services.update', $service),
            $this->payload($membership, ['price' => '45.500', 'duration_minutes' => 45, 'instructions' => 'إحضار التقارير']))
            ->assertSessionHasNoErrors();
        $this->assertSame(45500, $service->fresh()->price_minor);
        $this->assertSame(45, $service->fresh()->duration_minutes);
        $this->patch(route('doctor.practice.services.status', $service), ['is_active' => 1])->assertSessionHasNoErrors();
        $this->assertTrue($service->fresh()->is_active);
        $this->patch(route('doctor.practice.services.status', $service), ['is_active' => 0])->assertSessionHasNoErrors();
        $this->assertFalse($service->fresh()->is_active);
        $this->assertModelExists($service);
    }

    #[TestWith(['doctor'])]
    #[TestWith(['bookings'])]
    public function test_activation_requires_verified_doctor_and_enabled_facility(string $blocked): void
    {
        $membership = $this->membership();
        if ($blocked === 'doctor') {
            $membership->doctor->update(['verification_status' => 'pending']);
        } else {
            $membership->facility->update(['bookings_enabled' => false]);
        }
        $service = DoctorService::factory()->for($membership, 'doctorFacility')->create();
        $this->actingAs($membership->doctor, 'doctor')->patch(route('doctor.practice.services.status', $service), ['is_active' => 1])
            ->assertSessionHasErrors('is_active');
        $this->assertFalse($service->fresh()->is_active);
    }

    public function test_suspended_membership_blocks_reactivation_but_allows_pausing(): void
    {
        $membership = $this->membership();
        $service = DoctorService::factory()->for($membership, 'doctorFacility')->create(['is_active' => true]);
        $membership->update(['membership_status' => 'suspended']);
        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.services'))->assertViewHas('summary', fn ($summary) => $summary['active'] === 0);
        $this->patch(route('doctor.practice.services.status', $service), ['is_active' => 1])->assertSessionHasErrors('doctor_facility_id');
        $this->patch(route('doctor.practice.services.status', $service), ['is_active' => 0])->assertSessionHasNoErrors();
        $this->assertFalse($service->fresh()->is_active);
    }

    public function test_other_doctor_cannot_edit_update_or_toggle_a_service(): void
    {
        $service = DoctorService::factory()->create();
        $own = $this->membership();
        $this->actingAs($own->doctor, 'doctor')->get(route('doctor.practice.services', ['edit' => $service->id]))->assertNotFound();
        $this->put(route('doctor.practice.services.update', $service), $this->payload($own))->assertNotFound();
        $this->patch(route('doctor.practice.services.status', $service), ['is_active' => 1])->assertNotFound();
        $this->assertFalse($service->fresh()->is_active);
    }

    public function test_search_filters_and_text_escaping_work(): void
    {
        $membership = $this->membership();
        DoctorService::factory()->for($membership, 'doctorFacility')->create(['name' => 'استشارة خاصة', 'is_active' => false]);
        DoctorService::factory()->for($membership, 'doctorFacility')->create(['name' => '<script>alert(1)</script>', 'is_active' => true]);
        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.services', ['search' => 'استشارة', 'status' => 'paused']))
            ->assertOk()->assertSee('استشارة خاصة')->assertDontSee('alert(1)');
        $this->get(route('doctor.practice.services'))->assertOk()->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_professional_profile_links_to_real_services_and_counts_only_eligible_services(): void
    {
        $membership = $this->membership();
        DoctorService::factory()->for($membership, 'doctorFacility')->create(['is_active' => true]);
        DoctorService::factory()->for($membership, 'doctorFacility')->create(['is_active' => false]);
        DoctorService::factory()->create(['is_active' => true]);

        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.account.professional-profile'))
            ->assertOk()->assertViewHas('activeServiceCount', 1)
            ->assertSee('href="'.route('doctor.practice.services').'"', false);
    }

    public function test_services_guide_explains_activation_and_links_to_the_selected_facility_schedule(): void
    {
        $membership = $this->membership();

        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.services', ['membership' => $membership->id]))
            ->assertOk()->assertSee('حفظ الخدمة وحده لا يُظهر أوقاتًا للحجز.')
            ->assertSee('الخطوة التالية: الدوام والجدول')
            ->assertSee(route('doctor.practice.schedule', ['membership' => $membership->id]));
    }

    #[TestWith(['weekly'])]
    #[TestWith(['special'])]
    public function test_a_service_linked_to_a_schedule_cannot_move_to_another_facility(string $kind): void
    {
        $membership = $this->membership();
        $other = DoctorFacility::factory()->for($membership->doctor)->create();
        $service = DoctorService::factory()->for($membership)->create();
        $period = ['weekday' => 4, 'start' => '09:00', 'end' => '10:00', 'breaks' => [], 'service_ids' => [$service->id]];
        $schedule = DoctorSchedule::factory()->for($membership)->create(['periods' => $kind === 'weekly' ? [$period] : []]);
        if ($kind === 'special') {
            DoctorScheduleException::factory()->for($schedule)->create(['type' => 'special_hours', 'periods' => [$period]]);
        }

        $this->actingAs($membership->doctor, 'doctor')->put(route('doctor.practice.services.update', $service), $this->payload($other))
            ->assertSessionHasErrors(['doctor_facility_id' => 'الخدمة مرتبطة بفترات دوام أو ساعات خاصة. أزل ارتباطها من الجدول والاستثناءات قبل نقلها إلى منشأة أخرى.']);

        $this->assertSame($membership->id, $service->fresh()->doctor_facility_id);
    }

    public function test_an_unlinked_service_can_move_between_own_approved_facilities(): void
    {
        $membership = $this->membership();
        $other = DoctorFacility::factory()->for($membership->doctor)->create();
        $service = DoctorService::factory()->for($membership)->create();

        $this->actingAs($membership->doctor, 'doctor')->put(route('doctor.practice.services.update', $service), $this->payload($other))
            ->assertSessionHasNoErrors()->assertRedirect(route('doctor.practice.services'));

        $this->assertSame($other->id, $service->fresh()->doctor_facility_id);
    }

    public function test_ajax_service_create_update_and_pause_return_json_without_redirects(): void
    {
        $membership = $this->membership();
        $this->actingAs($membership->doctor, 'doctor');

        $this->postJson(route('doctor.practice.services.store'), $this->payload($membership))
            ->assertOk()->assertJsonPath('message', 'تمت إضافة الخدمة بنجاح.');
        $service = DoctorService::query()->sole();
        $this->assertSame('استشارة', $service->name);

        $this->putJson(route('doctor.practice.services.update', $service), $this->payload($membership, ['price' => '45.500', 'is_active' => 1]))
            ->assertOk()->assertJsonPath('message', 'تم تحديث الخدمة بنجاح.');
        $this->assertSame(45500, $service->fresh()->price_minor);
        $this->assertTrue($service->fresh()->is_active);

        $this->patchJson(route('doctor.practice.services.status', $service), ['is_active' => 0])
            ->assertOk()->assertJsonPath('message', 'تم إيقاف الحجز للخدمة مع الاحتفاظ ببياناتها.');
        $this->assertFalse($service->fresh()->is_active);
    }

    public function test_ajax_service_validation_and_ownership_are_still_enforced(): void
    {
        $membership = $this->membership();
        $foreign = DoctorService::factory()->for($this->membership())->create();
        $this->actingAs($membership->doctor, 'doctor');

        $this->postJson(route('doctor.practice.services.store'), $this->payload($membership, ['name' => '']))
            ->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->putJson(route('doctor.practice.services.update', $foreign), $this->payload($membership))
            ->assertNotFound();
        $this->assertSame(1, DoctorService::query()->count());
    }

    private function membership(): DoctorFacility
    {
        return DoctorFacility::factory()->for(Doctor::factory()->create(['verification_status' => 'verified']))
            ->for(Facility::factory()->create(['is_active' => true, 'bookings_enabled' => true]))->create();
    }

    /** @return array<string, mixed> */
    private function payload(DoctorFacility $membership, array $overrides = []): array
    {
        return array_replace([
            'doctor_facility_id' => $membership->id, 'name' => 'استشارة',
            'description' => 'تقييم ومتابعة.', 'price' => '30.000', 'currency' => 'JOD',
            'duration_minutes' => 30, 'instructions' => null, 'is_active' => 0,
        ], $overrides);
    }
}
