<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorPayment;
use App\Models\DoctorQueueEntry;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorCollectionPolicyTest extends TestCase
{
    use RefreshDatabase;

    #[TestWith([true])]
    #[TestWith([false])]
    public function test_template_payment_controls_preserve_real_collection_policy_and_financial_information(bool $required): void
    {
        config(['thera.doctor_deposit_percentage' => 17]);
        $doctor = Doctor::factory()->create();
        $doctor->account_preferences = ['require_collection_before_visit' => $required];
        $doctor->save();

        $response = $this->actingAs($doctor, 'doctor')->get(route('doctor.account.settings', ['tab' => 'payment']))
            ->assertOk()->assertSee('ليست بوابة دفع مفعّلة ولا عمولة للمنصة.')
            ->assertSee('تسجيل تحصيل يدوي مع سجل دفعات وإيصال.')
            ->assertSee('بدء الزيارة يتطلب الحضور والاستدعاء، والسداد الكامل عند تفعيل اشتراط التحصيل.')
            ->assertSee('لم تُربط بعد؛ قواعدها واتفاقات الطبيب والمنشأة تحتاج اعتمادًا مستقلًا.')
            ->assertSee('تسجيل الحضور والاستدعاء مطلوبان في الحالتين، ولا تتأثر الزيارات التي بدأت مسبقًا.')
            ->assertSee(route('doctor.finance.collections'));

        $document = new \DOMDocument;
        $previousErrors = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrors);
        }
        $beforeVisit = $document->getElementById('payment-remaining-before_visit');
        $afterVisit = $document->getElementById('payment-remaining-after_visit');
        $this->assertSame($required, $beforeVisit->hasAttribute('checked'));
        $this->assertSame(! $required, $afterVisit->hasAttribute('checked'));
        $this->assertSame('payment_policy[remaining_timing]', $beforeVisit->getAttribute('name'));
        $this->assertSame('17', $document->getElementById('deposit-percent')->getAttribute('value'));
        $this->assertSame($required, $doctor->fresh()->requiresCollectionBeforeVisit());
        $this->assertDatabaseEmpty('doctor_setting_events');
        $this->assertDatabaseEmpty('doctor_payments');
    }

    public function test_collection_is_required_by_default_and_reading_preferences_does_not_write(): void
    {
        $appointment = $this->appointment();
        $this->assertTrue($appointment->doctor->requiresCollectionBeforeVisit());
        $this->assertNull($appointment->doctor->fresh()->account_preferences);
        $this->assertSame('waiting_payment', $appointment->loadSum('payments as paid_minor', 'amount_minor')->queueState());
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertUnprocessable();
        DoctorQueueEntry::factory()->create(['doctor_appointment_id' => $appointment->id]);
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 1])->assertUnprocessable();
        $this->assertDatabaseEmpty('doctor_visits');
    }

    public function test_switch_is_audited_scoped_to_the_current_doctor_and_omission_preserves_it(): void
    {
        $appointment = $this->appointment();
        $other = Doctor::factory()->create();
        $data = $this->settings(false) + ['doctor_id' => $other->id];
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $this->assertFalse($appointment->doctor->fresh()->requiresCollectionBeforeVisit());
        $this->assertTrue($other->fresh()->requiresCollectionBeforeVisit());
        $this->assertDatabaseHas('doctor_setting_events', ['doctor_id' => $appointment->doctor_id, 'revision' => 1]);
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $this->assertDatabaseCount('doctor_setting_events', 1);
        unset($data['require_collection_before_visit']);
        $data['revision'] = 1;
        $data['availability'] = 'busy';
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $this->assertFalse($appointment->doctor->fresh()->requiresCollectionBeforeVisit());
        $this->get(route('doctor.account.settings', ['tab' => 'payment']))->assertOk()->assertSee('اشتراط التحصيل قبل بدء الزيارة')->assertViewHas('preferences', fn ($preferences) => ! $preferences['require_collection_before_visit']);
        $this->assertDatabaseEmpty('doctor_payments');
    }

    #[TestWith([0])]
    #[TestWith([5000])]
    public function test_disabled_policy_allows_call_and_visit_without_changing_the_outstanding_balance(int $paid): void
    {
        $appointment = $this->appointment();
        if ($paid) {
            DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id, 'amount_minor' => $paid]);
        }
        $this->putJson(route('doctor.account.settings.update'), $this->settings(false))->assertOk();
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertOk();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 2])->assertOk();
        $this->assertDatabaseCount('doctor_visits', 1);
        $this->assertSame($paid, (int) $appointment->payments()->sum('amount_minor'));
        $this->assertSame(20000 - $paid, $appointment->fresh()->loadSum('payments as paid_minor', 'amount_minor')->remainingMinor());
        $this->assertSame('confirmed', $appointment->fresh()->status);
    }

    #[TestWith(['arrival'])]
    #[TestWith(['date'])]
    #[TestWith(['status'])]
    #[TestWith(['membership'])]
    #[TestWith(['facility'])]
    public function test_disabling_collection_does_not_bypass_other_queue_requirements(string $missing): void
    {
        $appointment = $this->appointment();
        $this->putJson(route('doctor.account.settings.update'), $this->settings(false))->assertOk();
        match ($missing) {
            'arrival' => $appointment->update(['arrived_at' => null]),
            'date' => $appointment->update(['starts_at' => now()->addDay()]),
            'status' => $appointment->update(['status' => 'cancelled']),
            'membership' => $appointment->doctorFacility->update(['membership_status' => 'suspended']),
            'facility' => $appointment->doctorFacility->facility->update(['is_active' => false]),
        };
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertUnprocessable();
        DoctorQueueEntry::factory()->create(['doctor_appointment_id' => $appointment->id]);
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 1])->assertUnprocessable();
        $this->assertDatabaseEmpty('doctor_visits');
    }

    public function test_disabled_policy_still_requires_a_call_and_uses_the_latest_saved_policy_before_start(): void
    {
        $appointment = $this->appointment();
        $this->putJson(route('doctor.account.settings.update'), $this->settings(false))->assertOk();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 1])->assertUnprocessable();
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertOk();
        $data = $this->settings(true);
        $data['revision'] = 1;
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 2])->assertUnprocessable();
        $this->assertDatabaseEmpty('doctor_visits');
    }

    public function test_a_visit_already_started_remains_accessible_after_requiring_payment(): void
    {
        $appointment = $this->appointment();
        $this->putJson(route('doctor.account.settings.update'), $this->settings(false))->assertOk();
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertOk();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 2])->assertOk();
        $data = $this->settings(true);
        $data['revision'] = 1;
        $this->putJson(route('doctor.account.settings.update'), $data)->assertOk();
        $this->get(route('doctor.clinical.visit', ['visit' => DoctorVisit::sole()->id]))->assertOk();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 2])->assertOk();
        $this->assertDatabaseCount('doctor_visits', 1);
        $this->assertDatabaseEmpty('doctor_payments');
    }

    public function test_free_appointments_need_no_payment_even_with_the_requirement_enabled(): void
    {
        $appointment = $this->appointment();
        $appointment->update(['price_minor' => 0]);
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertOk();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 2])->assertOk();
        $this->assertDatabaseCount('doctor_visits', 1);
        $this->assertDatabaseEmpty('doctor_payments');
    }

    public function test_invalid_and_stale_policy_changes_cannot_silently_disable_the_requirement(): void
    {
        $appointment = $this->appointment();
        $data = $this->settings(false);
        $data['require_collection_before_visit'] = 'invalid';
        $this->putJson(route('doctor.account.settings.update'), $data)->assertUnprocessable()->assertJsonValidationErrors('require_collection_before_visit');
        $data = $this->settings(false);
        $data['revision'] = 999;
        $this->putJson(route('doctor.account.settings.update'), $data)->assertUnprocessable()->assertJsonValidationErrors('revision');
        $this->assertTrue($appointment->doctor->fresh()->requiresCollectionBeforeVisit());
        $this->assertDatabaseEmpty('doctor_setting_events');
    }

    #[TestWith(['doctor.appointments.index'])]
    #[TestWith(['doctor.appointments.show'])]
    public function test_collection_drawer_is_embedded_and_payment_refreshes_in_the_same_appointment_context(string $route): void
    {
        $appointment = $this->appointment();
        $parameters = ['date' => '2026-11-01', 'view' => 'list', 'collection' => $appointment->id];
        if ($route === 'doctor.appointments.show') {
            $parameters['appointment'] = $appointment->id;
        }
        $url = route($route, $parameters);
        $page = $this->get($url)->assertOk()->assertSee('data-finance-query-key="collection"', false)->assertSee('id="finance-payment-form"', false);
        preg_match('/<form id="finance-payment-form"[^>]*>/', $page->getContent(), $matches);
        $this->assertStringNotContainsString('data-ajax-follow', $matches[0]);
        $this->postJson(route('doctor.finance.collections.store', $appointment), ['revision' => 1, 'request_key' => (string) Str::uuid(), 'amount' => '5.000', 'method' => 'cash'])->assertOk();
        $this->get($url)->assertOk()->assertSee('15.000')->assertSee('عرض الإيصال وطباعته');
        $this->get(route($route, array_diff_key($parameters, ['collection' => true])))->assertOk()->assertDontSee('id="finance-payment-form"', false);
        $this->get(route('doctor.finance.collections', ['appointment' => $appointment->id]))->assertOk()->assertSee('data-finance-query-key="appointment"', false)->assertSee('data-ajax-follow', false);
        $this->assertDatabaseCount('doctor_payments', 1);
    }

    public function test_foreign_collection_and_mismatched_detail_drawers_are_not_accessible(): void
    {
        $appointment = $this->appointment();
        $foreign = DoctorAppointment::factory()->create();
        $this->get(route('doctor.appointments.index', ['collection' => $foreign->id]))->assertNotFound();
        $this->get(route('doctor.appointments.show', ['appointment' => $appointment->id, 'collection' => $foreign->id]))->assertNotFound();
        $own = DoctorAppointment::factory()->create(['doctor_id' => $appointment->doctor_id, 'doctor_facility_id' => $appointment->doctor_facility_id]);
        $this->get(route('doctor.appointments.show', ['appointment' => $appointment->id, 'collection' => $own->id]))->assertNotFound();
        $this->getJson(route('doctor.appointments.index', ['collection' => 'invalid']))->assertUnprocessable();
    }

    private function appointment(): DoctorAppointment
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-01 10:00', config('thera.schedule_timezone')));
        $appointment = DoctorAppointment::factory()->create(['starts_at' => now()->subHour(), 'ends_at' => now()->subMinutes(30), 'arrived_at' => now()->subMinutes(65)]);
        $appointment->doctor->update(['verification_status' => 'verified']);
        $this->actingAs($appointment->doctor, 'doctor');

        return $appointment;
    }

    /** @return array<string, mixed> */
    private function settings(bool $required): array
    {
        return ['revision' => 0, 'tab' => 'payment', 'availability' => 'unspecified', 'notifications' => array_fill_keys(array_keys(Doctor::NOTIFICATION_PREFERENCES), false), 'require_collection_before_visit' => $required];
    }
}
