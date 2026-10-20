<?php

namespace Tests\Feature\Doctor\Practice;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleException;
use App\Models\DoctorService;
use App\Models\Facility;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorScheduleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admins_cannot_manage_doctor_schedules(): void
    {
        $this->get(route('doctor.practice.schedule'))->assertRedirect(route('doctor.login'));
        $this->post(route('doctor.practice.schedule.store'))->assertRedirect(route('doctor.login'));
        $this->post(route('doctor.practice.schedule.exceptions.store'))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.practice.schedule'))->assertRedirect(route('doctor.login'));
    }

    public function test_doctor_can_save_and_reload_multiple_periods_services_and_breaks(): void
    {
        $membership = $this->membership();
        $service = $this->service($membership);
        $periods = [$this->period($service), $this->period($service, ['start' => '13:00', 'end' => '15:00', 'breaks' => []])];

        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.store'), $this->payload($membership, $periods))
            ->assertRedirect(route('doctor.practice.schedule', ['membership' => $membership->id]))->assertSessionHasNoErrors();

        $schedule = DoctorSchedule::query()->firstOrFail();
        $this->assertSame($periods, $schedule->periods);
        $this->assertSame(3, $schedule->daily_capacity);
        $this->assertSame(1, $schedule->revision);
        $this->get(route('doctor.practice.schedule'))->assertOk()->assertViewHas('schedule', fn ($value) => $value->id === $schedule->id)
            ->assertViewHas('summary', fn ($summary) => $summary['days'] === 1 && $summary['hours'] === 4.5 && $summary['breaks'] === 1)
            ->assertSee('bootstrap@5.3.3', false)->assertSee('schedule-mobile-list', false)->assertSee('select2@4.1.0', false);
    }

    public function test_periods_json_payload_is_validated_and_extra_fields_are_not_mass_assigned(): void
    {
        $membership = $this->membership();
        $service = $this->service($membership);
        $payload = $this->payload($membership, []);
        $payload['periods_payload'] = json_encode([$this->period($service)]);
        $payload['doctor_id'] = 999;
        $payload['id'] = 999;

        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.store'), $payload)->assertSessionHasNoErrors();

        $schedule = DoctorSchedule::query()->firstOrFail();
        $this->assertSame($membership->id, $schedule->doctor_facility_id);
        $this->assertSame(1, $schedule->revision);
        $this->assertSame([$this->period($service)], $schedule->periods);
        $this->post(route('doctor.practice.schedule.store'), array_replace($payload, ['periods_payload' => 'invalid JSON']))->assertSessionHasErrors('periods');
        $this->assertSame(1, $schedule->fresh()->revision);
    }

    #[TestWith(['pending'])]
    #[TestWith(['suspended'])]
    #[TestWith(['rejected'])]
    public function test_nonactive_memberships_are_read_only_and_reject_writes(string $status): void
    {
        $membership = $this->membership();
        $membership->update(['membership_status' => $status]);
        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.schedule', ['membership' => $membership->id]))
            ->assertOk()->assertViewHas('canEdit', false);
        $this->post(route('doctor.practice.schedule.store'), $this->payload($membership, []))->assertSessionHasErrors('doctor_facility_id');
        $this->assertDatabaseEmpty('doctor_schedules');
    }

    public function test_foreign_memberships_and_services_are_rejected(): void
    {
        $own = $this->membership();
        $other = $this->membership();
        $service = $this->service($other);
        $this->actingAs($own->doctor, 'doctor')->get(route('doctor.practice.schedule', ['membership' => $other->id]))->assertNotFound();
        $this->post(route('doctor.practice.schedule.store'), $this->payload($other, []))->assertSessionHasErrors('doctor_facility_id');
        $this->post(route('doctor.practice.schedule.store'), $this->payload($own, [$this->period($service)]))->assertSessionHasErrors('periods.0.service_ids');
        $this->assertDatabaseEmpty('doctor_schedules');
    }

    public function test_inactive_facility_cannot_receive_a_schedule(): void
    {
        $membership = $this->membership();
        $membership->facility->update(['is_active' => false]);
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.store'), $this->payload($membership, []))->assertSessionHasErrors('doctor_facility_id');
        $this->assertDatabaseEmpty('doctor_schedules');
    }

    #[TestWith(['weekday', 9, 'periods.0.weekday'])]
    #[TestWith(['start', '25:00', 'periods.0.start'])]
    #[TestWith(['end', '08:00', 'periods.0.end'])]
    #[TestWith(['service_ids', [], 'periods.0.service_ids'])]
    #[TestWith(['breaks', [['start' => '08:00', 'end' => '09:30']], 'periods.0.breaks'])]
    #[TestWith(['breaks', [['start' => '10:30', 'end' => '10:00']], 'periods.0.breaks'])]
    #[TestWith(['breaks', [['start' => '10:00', 'end' => '11:00'], ['start' => '10:30', 'end' => '11:30']], 'periods.0.breaks'])]
    #[TestWith(['breaks', [['start' => '09:00', 'end' => '12:00']], 'periods.0.service_ids'])]
    public function test_invalid_periods_and_breaks_are_not_saved(string $field, mixed $value, string $error): void
    {
        $membership = $this->membership();
        $service = $this->service($membership);
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.store'), $this->payload($membership, [$this->period($service, [$field => $value])]))
            ->assertSessionHasErrors($error);
        $this->assertDatabaseEmpty('doctor_schedules');
    }

    #[TestWith(['daily_capacity', -1])]
    #[TestWith(['daily_capacity', 501])]
    #[TestWith(['daily_capacity', 1.5])]
    #[TestWith(['is_active', 'enabled'])]
    public function test_invalid_capacity_and_activation_values_are_rejected(string $field, mixed $value): void
    {
        $membership = $this->membership();
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.store'), array_replace($this->payload($membership, []), [$field => $value]))->assertSessionHasErrors($field);
        $this->assertDatabaseEmpty('doctor_schedules');
    }

    public function test_self_overlaps_are_rejected_but_adjacent_periods_are_allowed(): void
    {
        $membership = $this->membership();
        $service = $this->service($membership);
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.store'), $this->payload($membership, [$this->period($service), $this->period($service, ['start' => '11:00', 'end' => '13:00', 'breaks' => []])]))->assertSessionHasErrors('periods');
        $this->assertDatabaseEmpty('doctor_schedules');
        $this->post(route('doctor.practice.schedule.store'), $this->payload($membership, [$this->period($service), $this->period($service, ['start' => '12:00', 'end' => '13:00', 'breaks' => []])]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('doctor_schedules', 1);
    }

    public function test_cross_facility_overlap_rolls_back_but_paused_schedules_do_not_block(): void
    {
        $first = $this->membership();
        $second = $this->membership($first->doctor);
        $a = $this->service($first);
        $b = $this->service($second);
        $this->actingAs($first->doctor, 'doctor')->post(route('doctor.practice.schedule.store'), $this->payload($first, [$this->period($a)]))->assertSessionHasNoErrors();
        $this->post(route('doctor.practice.schedule.store'), $this->payload($second, [$this->period($b)]))->assertSessionHasErrors('periods');
        $this->assertDatabaseCount('doctor_schedules', 1);
        $this->post(route('doctor.practice.schedule.store'), array_replace($this->payload($second, [$this->period($b)]), ['is_active' => 0]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('doctor_schedules', 2);
        $secondSchedule = $second->schedule()->firstOrFail();
        $this->post(route('doctor.practice.schedule.store'), array_replace($this->payload($second, [$this->period($b)]), ['revision' => $secondSchedule->revision]))->assertSessionHasErrors('periods');
        $this->assertFalse($secondSchedule->fresh()->is_active);
    }

    public function test_stale_revision_cannot_overwrite_a_newer_schedule(): void
    {
        $membership = $this->membership();
        $service = $this->service($membership);
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.store'), $this->payload($membership, [$this->period($service)]))->assertSessionHasNoErrors();
        $this->post(route('doctor.practice.schedule.store'), $this->payload($membership, []))->assertSessionHasErrors('revision');
        $schedule = $membership->schedule()->firstOrFail();
        $this->assertSame([$this->period($service)], $schedule->periods);
        $this->post(route('doctor.practice.schedule.store'), array_replace($this->payload($membership, []), ['revision' => 1, 'is_active' => 0]))->assertSessionHasNoErrors();
        $this->assertSame(2, $schedule->fresh()->revision);
        $this->assertSame([], $schedule->fresh()->periods);
    }

    public function test_preview_respects_breaks_duration_service_linkage_and_daily_capacity(): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $service = $this->service($membership);
        DoctorSchedule::factory()->for($membership)->create(['periods' => [$this->period($service)], 'daily_capacity' => 3]);
        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))
            ->assertOk()->assertViewHas('preview', fn ($preview) => $preview['slots'] === ['09:00', '09:30', '10:30', '11:00', '11:30'] && $preview['status'] === 'محدود' && $preview['capacity'] === 3);
        $unlinked = $this->service($membership);
        $this->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-15', 'service' => $unlinked->id]))->assertViewHas('preview', fn ($preview) => $preview['slots'] === []);
    }

    #[TestWith(['capacity'])]
    #[TestWith(['doctor'])]
    #[TestWith(['facility'])]
    #[TestWith(['service'])]
    #[TestWith(['membership'])]
    #[TestWith(['schedule'])]
    public function test_preview_is_unavailable_when_an_eligibility_condition_is_missing(string $condition): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $service = $this->service($membership);
        $schedule = DoctorSchedule::factory()->for($membership)->create(['periods' => [$this->period($service)]]);
        match ($condition) {
            'capacity' => $schedule->update(['daily_capacity' => 0]),
            'doctor' => $membership->doctor->update(['verification_status' => 'pending']),
            'facility' => $membership->facility->update(['bookings_enabled' => false]),
            'service' => $service->update(['is_active' => false]),
            'membership' => $membership->update(['membership_status' => 'suspended']),
            'schedule' => $schedule->update(['is_active' => false]),
        };
        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.schedule', ['membership' => $membership->id, 'preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))
            ->assertOk()->assertViewHas('preview', fn ($preview) => $preview['slots'] === [] && $preview['status'] === 'غير متاح');
    }

    public function test_exception_can_be_created_edited_and_deleted_and_overrides_only_its_date(): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $service = $this->service($membership);
        $schedule = DoctorSchedule::factory()->for($membership)->create(['periods' => [$this->period($service)]]);
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.exceptions.store'), $this->exceptionPayload($membership))
            ->assertSessionHasNoErrors();
        $exception = DoctorScheduleException::query()->firstOrFail();
        $this->assertSame('leave', $exception->type);
        $this->assertSame(2, $schedule->fresh()->revision);
        $this->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))->assertViewHas('preview', fn ($preview) => $preview['status'] === 'عطلة' && $preview['slots'] === []);
        $this->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-22', 'service' => $service->id]))->assertViewHas('preview', fn ($preview) => count($preview['slots']) === 5);
        $this->put(route('doctor.practice.schedule.exceptions.update', $exception), array_replace($this->exceptionPayload($membership), ['revision' => 2, 'type' => 'special_hours', 'periods' => [$this->period($service, ['start' => '13:00', 'end' => '14:00', 'breaks' => []])]]))->assertSessionHasNoErrors();
        $this->assertSame('special_hours', $exception->fresh()->type);
        $this->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))->assertViewHas('preview', fn ($preview) => $preview['slots'] === ['13:00', '13:30']);
        $this->delete(route('doctor.practice.schedule.exceptions.destroy', $exception), ['revision' => 3])->assertSessionHasNoErrors();
        $this->assertModelMissing($exception);
        $this->assertSame(4, $schedule->fresh()->revision);
    }

    public function test_duplicate_exception_and_stale_exception_changes_do_not_modify_data(): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $schedule = DoctorSchedule::factory()->for($membership)->create();
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.exceptions.store'), $this->exceptionPayload($membership))->assertSessionHasNoErrors();
        $this->post(route('doctor.practice.schedule.exceptions.store'), array_replace($this->exceptionPayload($membership), ['revision' => 2]))->assertSessionHasErrors('date');
        $exception = $schedule->exceptions()->firstOrFail();
        $this->put(route('doctor.practice.schedule.exceptions.update', $exception), $this->exceptionPayload($membership))->assertSessionHasErrors('revision');
        $this->delete(route('doctor.practice.schedule.exceptions.destroy', $exception), ['revision' => 1])->assertSessionHasErrors('revision');
        $this->assertDatabaseCount('doctor_schedule_exceptions', 1);
        $this->assertSame(2, $schedule->fresh()->revision);
    }

    #[TestWith(['date', '2026-10-13', 'date'])]
    #[TestWith(['date', '2026-02-30', 'date'])]
    #[TestWith(['date', '2027-10-15', 'date'])]
    #[TestWith(['type', 'anything', 'type'])]
    #[TestWith(['type', 'special_hours', 'periods'])]
    public function test_invalid_exception_dates_types_and_missing_special_hours_are_rejected(string $field, string $value, string $error): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        DoctorSchedule::factory()->for($membership)->create();
        $this->actingAs($membership->doctor, 'doctor')->post(route('doctor.practice.schedule.exceptions.store'), array_replace($this->exceptionPayload($membership), [$field => $value]))->assertSessionHasErrors($error);
        $this->assertDatabaseEmpty('doctor_schedule_exceptions');
    }

    public function test_foreign_exception_policy_and_endpoints_hide_its_existence(): void
    {
        $this->freezeCalendar();
        $own = $this->membership();
        $other = $this->membership();
        $schedule = DoctorSchedule::factory()->for($other)->create();
        $exception = DoctorScheduleException::factory()->for($schedule)->create(['date' => '2026-10-15']);
        $this->assertTrue(Gate::forUser($other->doctor)->allows('update', $exception));
        $this->assertFalse(Gate::forUser($own->doctor)->allows('update', $exception));
        $this->assertTrue(Gate::forUser($other->doctor)->allows('delete', $exception));
        $this->assertFalse(Gate::forUser($own->doctor)->allows('delete', $exception));
        $this->actingAs($own->doctor, 'doctor')->get(route('doctor.practice.schedule', ['membership' => $own->id, 'edit_exception' => $exception->id]))->assertNotFound();
        $this->put(route('doctor.practice.schedule.exceptions.update', $exception), $this->exceptionPayload($own))->assertNotFound();
        $this->delete(route('doctor.practice.schedule.exceptions.destroy', $exception), ['revision' => 1])->assertNotFound();
        $this->assertModelExists($exception);
    }

    public function test_special_hours_cross_facility_conflict_and_restoring_a_closed_day_are_checked(): void
    {
        $this->freezeCalendar();
        $a = $this->membership();
        $b = $this->membership($a->doctor);
        $sa = $this->service($a);
        $sb = $this->service($b);
        $first = DoctorSchedule::factory()->for($a)->create(['periods' => [$this->period($sa)]]);
        DoctorSchedule::factory()->for($b)->create(['periods' => [$this->period($sb, ['start' => '13:00', 'end' => '15:00', 'breaks' => []])]]);
        $this->actingAs($a->doctor, 'doctor')->post(route('doctor.practice.schedule.exceptions.store'), array_replace($this->exceptionPayload($b), ['type' => 'special_hours', 'periods' => [$this->period($sb)]]))->assertSessionHasErrors('periods');
        $this->assertDatabaseEmpty('doctor_schedule_exceptions');
        $this->post(route('doctor.practice.schedule.exceptions.store'), $this->exceptionPayload($a))->assertSessionHasNoErrors();
        $closed = $first->exceptions()->firstOrFail();
        $this->post(route('doctor.practice.schedule.exceptions.store'), array_replace($this->exceptionPayload($b), ['type' => 'special_hours', 'periods' => [$this->period($sb)]]))->assertSessionHasNoErrors();
        $this->delete(route('doctor.practice.schedule.exceptions.destroy', $closed), ['revision' => 2])->assertSessionHasErrors('periods');
        $this->assertModelExists($closed);
        $this->assertSame(2, $first->fresh()->revision);
    }

    public function test_preview_omits_elapsed_times_and_escapes_exception_notes(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-14 10:15:00', config('thera.schedule_timezone')));
        $membership = $this->membership();
        $service = $this->service($membership);
        $schedule = DoctorSchedule::factory()->for($membership)->create(['periods' => [$this->period($service, ['weekday' => 3])]]);
        DoctorScheduleException::factory()->for($schedule)->create(['date' => '2026-10-15', 'note' => '<script>alert(1)</script>']);
        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-14', 'service' => $service->id]))
            ->assertOk()->assertViewHas('preview', fn ($preview) => $preview['slots'] === ['10:30', '11:00', '11:30'])
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_schedule_guide_is_available_before_any_facility_or_schedule_is_created(): void
    {
        $doctor = Doctor::factory()->create();

        $this->actingAs($doctor, 'doctor')->get(route('doctor.practice.schedule'))->assertOk()
            ->assertSee('أول مرة تجهّز الدوام؟')
            ->assertSee('أدخل السعة اليومية')->assertSee('فعّل الجدول')->assertSee('احفظ ثم عاين')
            ->assertSee('preview-readiness-note', false);
    }

    public function test_preview_explains_all_missing_schedule_settings_without_changing_them(): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $service = $this->service($membership);
        $schedule = DoctorSchedule::factory()->for($membership)->create(['periods' => [$this->period($service)], 'daily_capacity' => 0, 'is_active' => false]);

        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))
            ->assertOk()->assertSee('فعّل الجدول ثم اضغط حفظ الجدول.')
            ->assertSee('أدخل سعة يومية أكبر من صفر ثم احفظ الجدول.');

        $this->assertFalse($schedule->fresh()->is_active);
        $this->assertSame(0, $schedule->fresh()->daily_capacity);
    }

    #[TestWith(['unlinked', 'الخدمة المختارة غير مرتبطة بفترات هذا اليوم.'])]
    #[TestWith(['too_long', 'مدة الخدمة أطول من الوقت المتصل المتاح بعد الاستراحات.'])]
    #[TestWith(['paused', 'فعّل الخدمة من صفحة الخدمات والأسعار.'])]
    public function test_preview_explains_the_specific_service_reason(string $condition, string $reason): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $service = $this->service($membership);
        DoctorSchedule::factory()->for($membership)->create(['periods' => [$this->period($service)]]);
        if ($condition === 'unlinked') {
            $service = $this->service($membership);
        } elseif ($condition === 'too_long') {
            $service->update(['duration_minutes' => 180]);
        } else {
            $service->update(['is_active' => false]);
        }

        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))
            ->assertOk()->assertSee($reason)->assertViewHas('preview', fn ($preview) => $preview['slots'] === []);
    }

    public function test_future_special_hours_are_included_in_readiness_even_without_weekly_periods(): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $service = $this->service($membership);
        $schedule = DoctorSchedule::factory()->for($membership)->create(['periods' => []]);
        DoctorScheduleException::factory()->for($schedule)->create(['date' => '2026-10-15', 'type' => 'special_hours', 'periods' => [$this->period($service)]]);

        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))
            ->assertOk()->assertViewHas('readinessSpecialPeriods', [$this->period($service)])
            ->assertViewHas('preview', fn ($preview) => count($preview['slots']) === 5);
    }

    public function test_preview_explains_when_all_today_slots_have_elapsed(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-15 17:00:00', config('thera.schedule_timezone')));
        $membership = $this->membership();
        $service = $this->service($membership);
        DoctorSchedule::factory()->for($membership)->create(['periods' => [$this->period($service)]]);

        $this->actingAs($membership->doctor, 'doctor')->get(route('doctor.practice.schedule', ['preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))
            ->assertOk()->assertSee('انتهت أوقات هذه الخدمة في التاريخ المختار.')
            ->assertViewHas('preview', fn ($preview) => $preview['slots'] === []);
    }

    public function test_ajax_weekly_save_and_exception_crud_keep_revisions_in_sync(): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $service = $this->service($membership);
        $this->actingAs($membership->doctor, 'doctor');

        $this->postJson(route('doctor.practice.schedule.store'), $this->payload($membership, [$this->period($service)]))
            ->assertOk()->assertJsonPath('message', 'تم حفظ الدوام والسعة اليومية والخدمات المرتبطة به.');
        $schedule = DoctorSchedule::query()->sole();
        $this->assertSame(1, $schedule->revision);
        $this->assertSame(3, $schedule->daily_capacity);

        $this->postJson(route('doctor.practice.schedule.exceptions.store'), $this->exceptionPayload($membership))
            ->assertOk()->assertJsonPath('message', 'تمت إضافة استثناء الجدول.');
        $exception = DoctorScheduleException::query()->sole();
        $this->assertSame(2, $schedule->fresh()->revision);

        $this->putJson(route('doctor.practice.schedule.exceptions.update', $exception), array_replace($this->exceptionPayload($membership), ['revision' => 2, 'note' => 'تم التعديل بالأجاكس']))
            ->assertOk()->assertJsonPath('message', 'تم تحديث استثناء الجدول.');
        $this->assertSame('تم التعديل بالأجاكس', $exception->fresh()->note);
        $this->assertSame(3, $schedule->fresh()->revision);

        $this->deleteJson(route('doctor.practice.schedule.exceptions.destroy', $exception), ['revision' => 3])
            ->assertOk()->assertJsonPath('message', 'تم حذف الاستثناء وإعادة تطبيق الجدول الأسبوعي.');
        $this->assertModelMissing($exception);
        $this->assertSame(4, $schedule->fresh()->revision);
    }

    public function test_ajax_stale_revision_returns_validation_without_overwriting_schedule(): void
    {
        $membership = $this->membership();
        $schedule = DoctorSchedule::factory()->for($membership)->create(['revision' => 2, 'daily_capacity' => 7]);

        $this->actingAs($membership->doctor, 'doctor')->postJson(route('doctor.practice.schedule.store'), $this->payload($membership, []))
            ->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertSame(2, $schedule->fresh()->revision);
        $this->assertSame(7, $schedule->fresh()->daily_capacity);
    }

    public function test_ajax_preview_validation_returns_json_and_valid_preview_renders_latest_saved_data(): void
    {
        $this->freezeCalendar();
        $membership = $this->membership();
        $service = $this->service($membership);
        DoctorSchedule::factory()->for($membership)->create(['periods' => [$this->period($service)]]);
        $this->actingAs($membership->doctor, 'doctor');

        $this->getJson(route('doctor.practice.schedule', ['membership' => $membership->id, 'preview' => 1]))
            ->assertUnprocessable()->assertJsonValidationErrors('service');
        $this->getJson(route('doctor.practice.schedule', ['membership' => $membership->id, 'preview' => 1, 'date' => '2026-10-15', 'service' => $service->id]))
            ->assertOk()->assertSee('practice-page-state', false)->assertSee('09:00')->assertSee('doctor-practice-ajax.js');
    }

    private function freezeCalendar(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-14 08:00:00', config('thera.schedule_timezone')));
    }

    private function membership(?Doctor $doctor = null): DoctorFacility
    {
        return DoctorFacility::factory()->for($doctor ?? Doctor::factory()->create(['verification_status' => 'verified']))
            ->for(Facility::factory()->create(['is_active' => true, 'bookings_enabled' => true]))->create();
    }

    private function service(DoctorFacility $membership): DoctorService
    {
        return DoctorService::factory()->for($membership)->create(['is_active' => true, 'duration_minutes' => 30]);
    }

    /** @param array<string,mixed> $overrides
     * @return array<string,mixed>
     */
    private function period(DoctorService $service, array $overrides = []): array
    {
        return array_replace(['weekday' => 4, 'start' => '09:00', 'end' => '12:00', 'breaks' => [['start' => '10:00', 'end' => '10:30']], 'service_ids' => [$service->id]], $overrides);
    }

    /** @param list<array<string,mixed>> $periods
     * @return array<string,mixed>
     */
    private function payload(DoctorFacility $membership, array $periods): array
    {
        return ['doctor_facility_id' => $membership->id, 'revision' => 0, 'daily_capacity' => 3, 'is_active' => 1, 'periods' => $periods];
    }

    /** @return array<string,mixed> */
    private function exceptionPayload(DoctorFacility $membership): array
    {
        return ['doctor_facility_id' => $membership->id, 'revision' => 1, 'date' => '2026-10-15', 'type' => 'leave', 'note' => 'إجازة تجريبية'];
    }
}
