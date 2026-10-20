<?php

namespace Tests\Feature\Doctor\Appointments;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use App\Models\DoctorPayment;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleException;
use App\Models\DoctorService;
use App\Models\Facility;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorAppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admins_cannot_access_appointments(): void
    {
        $this->get(route('doctor.appointments.index'))->assertRedirect(route('doctor.login'));
        $this->postJson(route('doctor.appointments.store'), [])->assertUnauthorized();
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.appointments.create'))->assertRedirect(route('doctor.login'));
    }

    public function test_booking_saves_server_price_duration_and_an_audit_event_without_payment(): void
    {
        $service = $this->readyService();

        $this->postJson(route('doctor.appointments.store'), $this->payload($service) + ['price_minor' => 1, 'status' => 'completed', 'source' => 'external', 'doctor_id' => 999])
            ->assertOk()->assertJsonStructure(['message', 'url']);

        $appointment = DoctorAppointment::query()->sole();
        $this->assertSame(20125, $appointment->price_minor);
        $this->assertSame(30, $appointment->duration_minutes);
        $this->assertSame('confirmed', $appointment->status);
        $this->assertSame('doctor', $appointment->source);
        $this->assertSame($service->doctorFacility->doctor_id, $appointment->doctor_id);
        $this->assertNull($appointment->arrived_at);
        $this->assertSame('09:00', $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->format('H:i'));
        $this->assertSame('09:30', $appointment->ends_at->setTimezone(config('thera.schedule_timezone'))->format('H:i'));
        $this->assertSame('created', $appointment->events()->sole()->action);
        $service->update(['price_minor' => 99000]);
        $this->assertSame(20125, $appointment->fresh()->price_minor);
    }

    public function test_identical_retries_return_same_booking_and_different_payload_reuse_is_rejected(): void
    {
        $service = $this->readyService();
        $payload = $this->payload($service);

        $first = $this->postJson(route('doctor.appointments.store'), $payload)->assertOk();
        $this->postJson(route('doctor.appointments.store'), $payload)->assertOk()->assertJsonPath('url', $first->json('url'));
        $this->postJson(route('doctor.appointments.store'), array_replace($payload, ['time' => '09:30']))->assertUnprocessable()->assertJsonValidationErrors('request_key');

        $this->assertSame(1, DoctorAppointment::query()->count());
        $this->assertSame(1, DoctorAppointment::query()->sole()->events()->count());
    }

    #[TestWith(['08:30'])]
    #[TestWith(['10:00'])]
    #[TestWith(['12:45'])]
    #[TestWith(['13:00'])]
    public function test_booking_rejects_time_outside_available_grid_or_during_break(string $time): void
    {
        $service = $this->readyService();

        $this->postJson(route('doctor.appointments.store'), array_replace($this->payload($service), ['time' => $time]))
            ->assertUnprocessable()->assertJsonValidationErrors('time');

        $this->assertDatabaseEmpty('doctor_appointments');
    }

    public function test_an_overlapping_booking_is_rejected_and_adjoining_slot_remains_available(): void
    {
        $service = $this->readyService();
        $this->book($service);

        $this->postJson(route('doctor.appointments.store'), $this->payload($service))->assertUnprocessable()->assertJsonValidationErrors('time');
        $this->postJson(route('doctor.appointments.store'), array_replace($this->payload($service), ['time' => '09:30']))->assertOk();

        $this->assertSame(2, DoctorAppointment::query()->count());
    }

    public function test_other_services_and_facilities_of_same_doctor_block_intersecting_time(): void
    {
        $service = $this->readyService();
        $otherMembership = DoctorFacility::factory()->for($service->doctorFacility->doctor)->for(Facility::factory()->create())->create();
        DoctorAppointment::factory()->create(['doctor_facility_id' => $otherMembership->id, 'starts_at' => $this->instant('09:00'), 'ends_at' => $this->instant('09:45')]);

        $response = $this->getJson(route('doctor.appointments.availability', ['doctor_service_id' => $service->id, 'date' => '2026-10-15']))->assertOk();

        $this->assertNotContains('09:00', $response->json('slots'));
        $this->assertNotContains('09:30', $response->json('slots'));
        $this->assertContains('10:30', $response->json('slots'));
    }

    public function test_capacity_is_shared_across_services_and_cancellation_releases_slot_and_quota(): void
    {
        $service = $this->readyService();
        $service->doctorFacility->schedule->update(['daily_capacity' => 1]);
        $appointment = $this->book($service);

        $this->getJson(route('doctor.appointments.availability', ['doctor_service_id' => $service->id, 'date' => '2026-10-15']))
            ->assertOk()->assertJsonPath('slots', [])->assertJsonPath('capacity', 0)->assertJsonPath('status', 'ممتلئ');
        $this->patchJson(route('doctor.appointments.status', $appointment), ['revision' => 1, 'action' => 'cancelled', 'reason' => 'طلب المريض'])
            ->assertOk();
        $response = $this->getJson(route('doctor.appointments.availability', ['doctor_service_id' => $service->id, 'date' => '2026-10-15']))->assertOk()->assertJsonPath('capacity', 1);

        $this->assertContains('09:00', $response->json('slots'));
        $this->assertSame('cancelled', $appointment->fresh()->status);
        $this->assertSame('طلب المريض', $appointment->events()->where('action', 'cancelled')->sole()->reason);
    }

    public function test_reschedule_preserves_original_price_and_duration_and_records_old_time(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);
        $service->update(['price_minor' => 99000, 'duration_minutes' => 60]);

        $this->putJson(route('doctor.appointments.update', $appointment), $this->reschedule($service, '09:30'))->assertOk();

        $appointment->refresh();
        $this->assertSame(20125, $appointment->price_minor);
        $this->assertSame(30, $appointment->duration_minutes);
        $this->assertSame('10:00', $appointment->ends_at->setTimezone(config('thera.schedule_timezone'))->format('H:i'));
        $event = $appointment->events()->where('action', 'rescheduled')->sole();
        $this->assertSame('09:00', CarbonImmutable::parse($event->before['starts_at'])->setTimezone(config('thera.schedule_timezone'))->format('H:i'));
        $this->assertSame(2, $appointment->revision);
    }

    public function test_failed_reschedule_preserves_original_appointment_and_history(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);
        $this->postJson(route('doctor.appointments.store'), array_replace($this->payload($service), ['time' => '09:30']))->assertOk();

        $this->putJson(route('doctor.appointments.update', $appointment), $this->reschedule($service, '09:30'))->assertUnprocessable()->assertJsonValidationErrors('time');

        $this->assertSame('09:00', $appointment->fresh()->starts_at->setTimezone(config('thera.schedule_timezone'))->format('H:i'));
        $this->assertSame(1, $appointment->events()->count());
        $this->assertSame(1, $appointment->fresh()->revision);
    }

    public function test_current_appointment_is_excluded_from_reschedule_capacity_and_overlap(): void
    {
        $service = $this->readyService();
        $service->doctorFacility->schedule->update(['daily_capacity' => 1]);
        $appointment = $this->book($service);

        $this->putJson(route('doctor.appointments.update', $appointment), $this->reschedule($service, '09:00'))->assertOk();

        $this->assertSame(1, DoctorAppointment::query()->count());
        $this->assertSame(2, $appointment->fresh()->revision);
    }

    public function test_foreign_services_appointments_contacts_and_calendar_are_isolated(): void
    {
        $foreignService = $this->readyService();
        $foreign = $this->book($foreignService);
        $own = $this->readyService();

        $this->get(route('doctor.appointments.show', ['appointment' => $foreign->id]))->assertNotFound();
        $this->get(route('doctor.appointments.edit', $foreign))->assertNotFound();
        $this->postJson(route('doctor.appointments.store'), $this->payload($foreignService))->assertNotFound();
        $this->patchJson(route('doctor.appointments.status', $foreign), ['action' => 'cancelled', 'revision' => 1, 'reason' => 'غير مخول'])->assertNotFound();
        $this->getJson(route('doctor.appointments.availability', ['doctor_service_id' => $own->id, 'date' => '2026-10-15', 'appointment' => $foreign->id]))->assertNotFound();
        $this->getJson(route('doctor.appointments.contacts', ['q' => 'مريض']))->assertOk()->assertExactJson(['results' => []]);
        $this->get(route('doctor.appointments.index', ['date' => '2026-10-15', 'view' => 'week']))->assertOk()->assertDontSee('مريض تجربة الحجز');

        $this->assertSame('confirmed', $foreign->fresh()->status);
    }

    public function test_stale_revision_does_not_cancel_or_reschedule_appointment(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);
        $appointment->update(['revision' => 2]);

        $this->putJson(route('doctor.appointments.update', $appointment), $this->reschedule($service, '09:30'))->assertUnprocessable()->assertJsonValidationErrors('revision');
        $this->patchJson(route('doctor.appointments.status', $appointment), ['action' => 'cancelled', 'revision' => 1, 'reason' => 'قديم'])->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertSame('confirmed', $appointment->fresh()->status);
        $this->assertSame(1, $appointment->events()->count());
    }

    public function test_arrival_is_separate_from_appointment_and_blocks_reschedule_and_no_show(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);

        $this->patchJson(route('doctor.appointments.status', $appointment), ['action' => 'arrived', 'revision' => 1])->assertOk();
        $this->putJson(route('doctor.appointments.update', $appointment), array_replace($this->reschedule($service, '09:30'), ['revision' => 2]))->assertUnprocessable();
        $this->patchJson(route('doctor.appointments.status', $appointment), ['action' => 'no_show', 'revision' => 2, 'reason' => 'لم يحضر'])->assertUnprocessable();

        $this->assertNotNull($appointment->fresh()->arrived_at);
        $this->assertSame('confirmed', $appointment->fresh()->status);
    }

    public function test_no_show_requires_end_time_and_reason_and_cannot_be_repeated(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);
        $data = ['action' => 'no_show', 'revision' => 1, 'reason' => 'انتهى الوقت دون حضور'];

        $this->patchJson(route('doctor.appointments.status', $appointment), $data)->assertUnprocessable()->assertJsonValidationErrors('action');
        $this->travelTo($this->instant('09:31'));
        $this->patchJson(route('doctor.appointments.status', $appointment), array_replace($data, ['reason' => '']))->assertUnprocessable()->assertJsonValidationErrors('reason');
        $this->patchJson(route('doctor.appointments.status', $appointment), $data)->assertOk();
        $this->patchJson(route('doctor.appointments.status', $appointment), array_replace($data, ['revision' => 2]))->assertUnprocessable()->assertJsonValidationErrors('revision');

        $this->assertSame('no_show', $appointment->fresh()->status);
    }

    #[TestWith(['verification'])]
    #[TestWith(['membership'])]
    #[TestWith(['facility'])]
    #[TestWith(['bookings'])]
    #[TestWith(['service'])]
    #[TestWith(['schedule'])]
    #[TestWith(['capacity'])]
    #[TestWith(['leave'])]
    public function test_ineligible_booking_prerequisites_block_confirmation(string $kind): void
    {
        $service = $this->readyService();
        match ($kind) {
            'verification' => $service->doctorFacility->doctor->update(['verification_status' => 'submitted']),
            'membership' => $service->doctorFacility->update(['membership_status' => 'suspended']),
            'facility' => $service->doctorFacility->facility->update(['is_active' => false]),
            'bookings' => $service->doctorFacility->facility->update(['bookings_enabled' => false]),
            'service' => $service->update(['is_active' => false]),
            'schedule' => $service->doctorFacility->schedule->update(['is_active' => false]),
            'capacity' => $service->doctorFacility->schedule->update(['daily_capacity' => 0]),
            'leave' => DoctorScheduleException::factory()->for($service->doctorFacility->schedule)->create(['date' => '2026-10-15', 'type' => 'leave', 'periods' => null]),
        };

        $this->postJson(route('doctor.appointments.store'), $this->payload($service))->assertUnprocessable()->assertJsonValidationErrors('time');

        $this->assertDatabaseEmpty('doctor_appointments');
    }

    public function test_required_data_invalid_phone_and_past_date_are_validated(): void
    {
        $service = $this->readyService();

        $this->postJson(route('doctor.appointments.store'), [])->assertUnprocessable()->assertJsonValidationErrors(['patient_name', 'patient_phone', 'doctor_service_id', 'date', 'time', 'request_key']);
        $this->postJson(route('doctor.appointments.store'), array_replace($this->payload($service), ['patient_phone' => 'abc', 'date' => '2026-10-14']))->assertUnprocessable()->assertJsonValidationErrors(['patient_phone', 'date']);

        $this->assertDatabaseEmpty('doctor_appointments');
    }

    public function test_patient_fields_are_escaped_and_filters_and_contact_lookup_work(): void
    {
        $service = $this->readyService();
        $name = '<script>alert(1)</script>';
        $this->postJson(route('doctor.appointments.store'), array_replace($this->payload($service), ['patient_name' => $name]))->assertOk();
        $appointment = DoctorAppointment::query()->sole();

        $this->get(route('doctor.appointments.show', ['appointment' => $appointment->id]))->assertOk()->assertDontSee($name, false)->assertSee('&lt;script&gt;', false);
        $this->get(route('doctor.appointments.index', ['date' => '2026-10-15', 'search' => 'alert', 'view' => 'list']))->assertOk()->assertSee('&lt;script&gt;', false);
        $this->getJson(route('doctor.appointments.contacts', ['q' => 'alert']))->assertOk()->assertJsonPath('results.0.name', $name);
    }

    public function test_appointments_open_as_month_calendar_by_default_and_daily_list_remains_optional(): void
    {
        $this->readyService();

        $response = $this->get(route('doctor.appointments.index'));

        $response->assertOk()->assertViewHas('mode', 'month')->assertSee('تقويم المواعيد الشهري')->assertSee('appointments-month-grid', false)->assertDontSee('<table', false);
        $this->get(route('doctor.appointments.index', ['view' => 'list']))->assertOk()->assertViewHas('mode', 'list')->assertSee('قائمة مواعيد اليوم الزمنية')->assertDontSee('<table', false);
    }

    public function test_month_calendar_aligns_weekdays_and_counts_only_own_bookings_without_patient_names(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);
        DoctorAppointment::factory()->create(['starts_at' => $this->instant('09:00'), 'ends_at' => $this->instant('09:30')]);

        $response = $this->get(route('doctor.appointments.index', ['view' => 'month', 'date' => '2026-10-15']));

        $response->assertOk()->assertDontSee($appointment->patient_name)->assertSee('appointments-month-grid', false);
        $overview = $response->viewData('monthOverview');
        $this->assertCount(35, $overview['days']);
        $this->assertSame('2026-09-27', $overview['days'][0]['date']->toDateString());
        $this->assertSame('2026-10-31', $overview['days'][34]['date']->toDateString());
        $this->assertSame(1, $overview['count']);
        $this->assertSame(1, $overview['todayCount']);
        $cell = collect($overview['days'])->first(fn ($cell) => $cell['today']);
        $this->assertSame(1, $cell['count']);
        $this->assertSame($cell['capacity'] - 1, $cell['remaining']);
    }

    public function test_month_calendar_respects_exceptions_and_cancelled_bookings_do_not_use_capacity(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);
        $appointment->update(['status' => 'cancelled']);
        $schedule = $service->doctorFacility->schedule;
        $schedule->exceptions()->create(['date' => '2026-10-22', 'type' => 'leave', 'periods' => []]);
        $schedule->exceptions()->create(['date' => '2026-10-19', 'type' => 'special_hours', 'periods' => [['start' => '14:00', 'end' => '16:00', 'breaks' => [], 'service_ids' => [$service->id]]]]);

        $response = $this->get(route('doctor.appointments.index', ['view' => 'month', 'date' => '2026-10-15']))->assertOk();

        $days = collect($response->viewData('monthOverview')['days'])->keyBy(fn ($cell) => $cell['date']->toDateString());
        $this->assertSame($days['2026-10-15']['capacity'], $days['2026-10-15']['remaining']);
        $this->assertSame('off', $days['2026-10-22']['category']);
        $this->assertSame('إجازة', $days['2026-10-22']['offLabel']);
        $this->assertSame('partial', $days['2026-10-19']['category']);
        $this->assertSame('16:00', $days['2026-10-19']['closingTime']);
    }

    public function test_calendar_categories_can_hide_all_days_and_daily_alias_still_renders_list(): void
    {
        $this->readyService();

        $response = $this->get(route('doctor.appointments.index', ['view' => 'month', 'date' => '2026-10-15', 'categories_present' => 1]))->assertOk();

        $this->assertSame(31, substr_count($response->getContent(), 'calendar-filtered'));
        $this->get(route('doctor.appointments.index', ['view' => 'day', 'date' => '2026-10-15']))->assertOk()->assertViewHas('mode', 'list');
    }

    #[TestWith([0, 0, 'off', false])]
    #[TestWith([1, 1, 'limited', true])]
    #[TestWith([5, 4, 'limited', false])]
    #[TestWith([5, 1, 'available', false])]
    public function test_monthly_day_state_reflects_daily_capacity(int $capacity, int $used, string $category, bool $full): void
    {
        $service = $this->readyService();
        $service->doctorFacility->schedule->update(['daily_capacity' => $capacity]);
        DoctorAppointment::factory()->count($used)->create(['doctor_facility_id' => $service->doctor_facility_id, 'doctor_service_id' => $service->id, 'starts_at' => $this->instant('09:00'), 'ends_at' => $this->instant('09:30')]);

        $response = $this->get(route('doctor.appointments.index', ['view' => 'month', 'date' => '2026-10-15']))->assertOk();

        $cell = collect($response->viewData('monthOverview')['days'])->first(fn ($cell) => $cell['today']);
        $this->assertSame($category, $cell['category']);
        $this->assertSame($full, $cell['full']);
        $this->assertSame($capacity - $used, $cell['remaining']);
    }

    public function test_month_counts_are_not_truncated_to_the_old_pagination_limit(): void
    {
        $service = $this->readyService();
        DoctorAppointment::factory()->count(201)->create(['doctor_facility_id' => $service->doctor_facility_id, 'doctor_service_id' => $service->id, 'starts_at' => $this->instant('09:00'), 'ends_at' => $this->instant('09:30')]);

        $response = $this->get(route('doctor.appointments.index', ['view' => 'month', 'date' => '2026-10-15']))->assertOk();

        $overview = $response->viewData('monthOverview');
        $this->assertSame(201, $overview['count']);
        $this->assertSame(201, collect($overview['days'])->first(fn ($cell) => $cell['today'])['count']);
    }

    public function test_week_board_filters_arrival_and_weekday_without_exposing_other_doctors(): void
    {
        $service = $this->readyService();
        $arrived = $this->book($service);
        $arrived->update(['arrived_at' => $this->instant('08:55')]);
        $this->postJson(route('doctor.appointments.store'), array_replace($this->payload($service), ['time' => '09:30', 'patient_name' => 'موعد لم يصل']))->assertOk();
        DoctorAppointment::factory()->create(['patient_name' => 'موعد لطبيب آخر', 'starts_at' => $this->instant('09:00')]);

        $response = $this->get(route('doctor.appointments.index', ['view' => 'week', 'date' => '2026-10-15', 'arrival' => 'arrived', 'weekday' => 4]));

        $response->assertOk()->assertSee('weekly-appt-item', false)->assertSee($arrived->patient_name)->assertDontSee('موعد لم يصل')->assertDontSee('موعد لطبيب آخر');
        $this->assertCount(1, $response->viewData('calendarDays'));
        $this->assertSame(4, $response->viewData('calendarDays')[0]['date']->dayOfWeek);
        $this->getJson(route('doctor.appointments.index', ['arrival' => 'invalid', 'weekday' => 8]))->assertUnprocessable()->assertJsonValidationErrors(['arrival', 'weekday']);
    }

    public function test_daily_timeline_displays_real_arrival_without_claiming_payment_or_readiness(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);
        $appointment->update(['arrived_at' => $this->instant('08:55')]);

        $response = $this->get(route('doctor.appointments.index', ['view' => 'day']));

        $response->assertOk()->assertSee('timeline-row has-arrived', false)->assertSee('حضور مسجل')->assertSee('وصل — بانتظار التحصيل')->assertSee('تفاصيل الموعد')->assertDontSee('class="appointment-status payment-partial"', false);
        $this->assertSame(0, $response->viewData('summary')['ready']);
        $this->assertSame(1, $response->viewData('summary')['waiting']);
    }

    public function test_service_change_is_rejected_after_a_payment_record_exists(): void
    {
        $service = $this->readyService();
        $appointment = $this->book($service);
        DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id]);
        $other = DoctorService::factory()->for($service->doctorFacility)->create(['is_active' => true]);

        $this->putJson(route('doctor.appointments.update', $appointment), $this->reschedule($other, '09:30'))->assertUnprocessable()->assertJsonValidationErrors('doctor_service_id');

        $this->assertSame($service->id, $appointment->fresh()->doctor_service_id);
        $this->assertSame(1000, (int) $appointment->payments()->sum('amount_minor'));
    }

    public function test_daily_drawer_filters_combine_search_status_arrival_and_payment(): void
    {
        $service = $this->readyService();
        $matching = $this->book($service);
        $matching->update(['patient_name' => 'ريم خليل', 'arrived_at' => $this->instant('08:55')]);
        DoctorPayment::factory()->create(['doctor_appointment_id' => $matching->id, 'amount_minor' => $matching->price_minor]);
        $this->postJson(route('doctor.appointments.store'), array_replace($this->payload($service), ['time' => '09:30', 'patient_name' => 'سامي منصور']))->assertOk();
        $filters = ['view' => 'list', 'date' => '2026-10-15', 'search' => 'ريم', 'status' => 'confirmed', 'arrival' => 'arrived', 'payment_status' => 'paid'];

        $response = $this->get(route('doctor.appointments.index', $filters));

        $response->assertOk()->assertSee('تصفية المواعيد')->assertSee('التصفية تلقائية')->assertDontSee('تطبيق التصفية');
        $this->assertSame([$matching->id], $response->viewData('appointments')->pluck('id')->all());
        $this->get(route('doctor.appointments.index', array_replace($filters, ['arrival' => 'not_arrived'])))
            ->assertOk()->assertViewHas('appointments', fn ($items) => $items->isEmpty());
    }

    private function readyService(): DoctorService
    {
        $this->travelTo($this->instant('08:00'));
        $membership = DoctorFacility::factory()->for(Doctor::factory()->create(['verification_status' => 'verified']))->for(Facility::factory()->create(['is_active' => true, 'bookings_enabled' => true]))->create();
        $service = DoctorService::factory()->for($membership)->create(['name' => 'استشارة الحجز', 'price_minor' => 20125, 'currency' => 'JOD', 'duration_minutes' => 30, 'is_active' => true]);
        DoctorSchedule::factory()->for($membership)->create(['periods' => [['weekday' => 4, 'start' => '09:00', 'end' => '13:00', 'breaks' => [['start' => '10:00', 'end' => '10:30']], 'service_ids' => [$service->id]]]]);
        $this->actingAs($membership->doctor, 'doctor');

        return $service;
    }

    private function instant(string $time): CarbonImmutable
    {
        return CarbonImmutable::parse('2026-10-15 '.$time, config('thera.schedule_timezone'))->utc();
    }

    /** @return array<string, mixed> */
    private function payload(DoctorService $service): array
    {
        return ['doctor_service_id' => $service->id, 'date' => '2026-10-15', 'time' => '09:00', 'patient_name' => 'مريض تجربة الحجز', 'patient_phone' => '+962790000777', 'request_key' => (string) Str::uuid()];
    }

    private function book(DoctorService $service): DoctorAppointment
    {
        $this->postJson(route('doctor.appointments.store'), $this->payload($service))->assertOk();

        return DoctorAppointment::query()->latest('id')->firstOrFail();
    }

    /** @return array<string, mixed> */
    private function reschedule(DoctorService $service, string $time): array
    {
        return ['doctor_service_id' => $service->id, 'date' => '2026-10-15', 'time' => $time, 'revision' => 1, 'reason' => 'طلب المريض تغيير الوقت'];
    }
}
