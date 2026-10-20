<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorPayment;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorCollectionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_admin_cannot_access_doctor_collections(): void
    {
        $this->get(route('doctor.finance.collections'))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.finance.collections'))->assertRedirect(route('doctor.login'));
    }

    public function test_partial_and_final_payment_preserve_precision_and_issue_stable_receipts(): void
    {
        $appointment = $this->appointment();
        $payload = $this->payload('10.125');

        $response = $this->postJson(route('doctor.finance.collections.store', $appointment), $payload);

        $response->assertOk()->assertJsonStructure(['message', 'url', 'receipt_url']);
        $payment = DoctorPayment::query()->sole();
        $this->assertSame(10125, $payment->amount_minor);
        $this->assertSame('JOD', $payment->currency);
        $this->assertSame(2, $appointment->fresh()->revision);
        $this->assertSame('payment_recorded', $appointment->events()->sole()->action);
        $appointment->refresh()->loadSum('payments as paid_minor', 'amount_minor');
        $this->assertSame('partial', $appointment->paymentStatus());
        $this->assertSame(10000, $appointment->remainingMinor());
        $this->get($response->json('receipt_url'))->assertOk()->assertSee($payment->receiptReference())->assertSee('10.125');

        $this->postJson(route('doctor.finance.collections.store', $appointment), array_replace($this->payload('10.000'), ['revision' => 2]))->assertOk();
        $this->assertSame(20125, (int) $appointment->payments()->sum('amount_minor'));
        $this->assertSame('confirmed', $appointment->fresh()->status);
        $this->assertNull($appointment->fresh()->arrived_at);
    }

    public function test_retry_does_not_duplicate_payment_and_changed_payload_with_same_key_is_rejected(): void
    {
        $appointment = $this->appointment();
        $payload = $this->payload('5.000');

        $first = $this->postJson(route('doctor.finance.collections.store', $appointment), $payload)->assertOk();
        $this->postJson(route('doctor.finance.collections.store', $appointment), $payload)->assertOk()->assertJsonPath('receipt_url', $first->json('receipt_url'));
        $this->postJson(route('doctor.finance.collections.store', $appointment), array_replace($payload, ['amount' => '6.000']))->assertUnprocessable()->assertJsonValidationErrors('request_key');

        $this->assertDatabaseCount('doctor_payments', 1);
        $this->assertSame(1, $appointment->events()->count());
    }

    #[TestWith(['0'])]
    #[TestWith(['-1'])]
    #[TestWith(['1.0001'])]
    #[TestWith(['21.000'])]
    #[TestWith(['1e1'])]
    public function test_invalid_or_excess_amount_does_not_write_money(string $amount): void
    {
        $appointment = $this->appointment();

        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload($amount))->assertUnprocessable()->assertJsonValidationErrors('amount');

        $this->assertDatabaseEmpty('doctor_payments');
        $this->assertSame(1, $appointment->fresh()->revision);
    }

    public function test_two_decimal_currency_rejects_a_third_decimal_and_does_not_trust_client_currency(): void
    {
        $appointment = $this->appointment(['currency' => 'ILS', 'price_minor' => 1250]);

        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('1.001'))->assertUnprocessable()->assertJsonValidationErrors('amount');
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('12.50') + ['currency' => 'USD', 'doctor_id' => 999])->assertOk();

        $this->assertSame(1250, DoctorPayment::query()->sole()->amount_minor);
        $this->assertSame('ILS', DoctorPayment::query()->sole()->currency);
        $this->assertSame($appointment->doctor_id, DoctorPayment::query()->sole()->doctor_id);
    }

    public function test_bank_transfer_requires_a_reference(): void
    {
        $appointment = $this->appointment();

        $this->postJson(route('doctor.finance.collections.store', $appointment), array_replace($this->payload('1.000'), ['method' => 'bank_transfer']))->assertUnprocessable()->assertJsonValidationErrors('reference');

        $this->assertDatabaseEmpty('doctor_payments');
    }

    #[TestWith(['cancelled'])]
    #[TestWith(['no_show'])]
    public function test_closed_appointments_cannot_be_collected(string $status): void
    {
        $appointment = $this->appointment(['status' => $status]);

        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('1.000'))->assertUnprocessable();

        $this->assertDatabaseEmpty('doctor_payments');
    }

    public function test_foreign_payments_receipts_and_queue_actions_are_not_disclosed(): void
    {
        $appointment = $this->appointment();
        $payment = DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id]);
        $this->actingAs(Doctor::factory()->create(), 'doctor');

        $this->get(route('doctor.finance.collections', ['appointment' => $appointment->id]))->assertNotFound();
        $this->get(route('doctor.finance.receipt', $payment))->assertNotFound();
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('1.000'))->assertNotFound();
        $this->postJson(route('doctor.finance.reverse', $payment), ['reason' => 'تصحيح قيد', 'revision' => 1, 'request_key' => (string) Str::uuid()])->assertNotFound();
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertNotFound();

        $this->assertDatabaseCount('doctor_payments', 1);
        $this->assertDatabaseEmpty('doctor_queue_entries');
    }

    public function test_reversal_preserves_original_and_restores_due_without_duplicate_reversal(): void
    {
        $appointment = $this->appointment();
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('10.000'))->assertOk();
        $payment = DoctorPayment::query()->sole();
        $payload = ['reason' => 'سجل المبلغ للموعد الخطأ', 'revision' => 2, 'request_key' => (string) Str::uuid()];

        $this->postJson(route('doctor.finance.reverse', $payment), $payload)->assertOk();
        $this->postJson(route('doctor.finance.reverse', $payment), $payload)->assertOk();
        $this->postJson(route('doctor.finance.reverse', $payment), array_replace($payload, ['revision' => 3, 'request_key' => (string) Str::uuid()]))->assertUnprocessable();

        $this->assertSame(10000, $payment->fresh()->amount_minor);
        $this->assertSame(-10000, $payment->reversal()->sole()->amount_minor);
        $this->assertSame(0, (int) $appointment->payments()->sum('amount_minor'));
        $this->assertDatabaseCount('doctor_payments', 2);
        $this->assertSame(2, $appointment->events()->count());
    }

    public function test_call_requires_arrival_and_full_payment_and_is_idempotent(): void
    {
        $appointment = $this->appointment();
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('20.125'))->assertOk();

        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 2])->assertUnprocessable();
        $appointment->update(['arrived_at' => CarbonImmutable::now('UTC')]);
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 2])->assertOk();
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 2])->assertOk();

        $this->assertDatabaseCount('doctor_queue_entries', 1);
        $this->assertSame(1, $appointment->events()->where('action', 'patient_called')->count());
        $this->assertSame('confirmed', $appointment->fresh()->status);
        $payment = DoctorPayment::query()->sole();
        $this->postJson(route('doctor.finance.reverse', $payment), ['reason' => 'تصحيح بعد الاستدعاء', 'revision' => 3, 'request_key' => (string) Str::uuid()])->assertUnprocessable();
        $this->assertDatabaseCount('doctor_payments', 1);
    }

    public function test_arrived_but_unpaid_patient_is_not_ready_and_free_service_needs_no_fake_payment(): void
    {
        $appointment = $this->appointment(['arrived_at' => CarbonImmutable::now('UTC')]);

        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertUnprocessable();
        $appointment->update(['price_minor' => 0]);
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertOk();

        $this->assertDatabaseEmpty('doctor_payments');
        $this->assertDatabaseCount('doctor_queue_entries', 1);
    }

    public function test_receipt_freezes_snapshot_and_escapes_user_content(): void
    {
        $appointment = $this->appointment(['patient_name' => '<script>bad</script>']);
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('1.000'))->assertOk();
        $payment = DoctorPayment::query()->sole();
        $appointment->update(['patient_name' => 'اسم آخر']);

        $this->get(route('doctor.finance.receipt', $payment))->assertOk()->assertSee('&lt;script&gt;bad&lt;/script&gt;', false)->assertDontSee('<script>bad</script>', false)->assertDontSee('اسم آخر');
    }

    public function test_collection_filters_do_not_add_different_currencies_together(): void
    {
        $appointment = $this->appointment();
        DoctorAppointment::factory()->create(['doctor_facility_id' => $appointment->doctor_facility_id, 'currency' => 'ILS', 'price_minor' => 3000, 'starts_at' => $appointment->starts_at]);
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('20.125'))->assertOk();

        $response = $this->get(route('doctor.finance.collections', ['date' => '2026-10-15', 'payment_status' => 'settled']))->assertOk();

        $this->assertSame(20125, $response->viewData('totals')['JOD']['paid']);
        $this->assertCount(1, $response->viewData('totals'));
        $this->get(route('doctor.finance.collections', ['appointment' => $appointment->id]))->assertOk()->assertSee('سجل الدفعات');
    }

    #[TestWith(['paid', '20.125', 1])]
    #[TestWith(['paid', '5.000', 0])]
    #[TestWith(['partial', '5.000', 1])]
    #[TestWith(['partial', '20.125', 0])]
    #[TestWith(['unpaid', '5.000', 0])]
    public function test_appointment_payment_filters_use_ledger_totals(string $filter, string $amount, int $expected): void
    {
        $appointment = $this->appointment();
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload($amount))->assertOk();

        $response = $this->get(route('doctor.appointments.index', ['view' => 'week', 'date' => '2026-10-15', 'payment_status' => $filter]))->assertOk();

        $this->assertSame($expected, $response->viewData('appointments')->total());
    }

    public function test_stale_revision_and_inactive_membership_do_not_record_money(): void
    {
        $appointment = $this->appointment();
        $this->postJson(route('doctor.finance.collections.store', $appointment), array_replace($this->payload('1.000'), ['revision' => 99]))->assertUnprocessable()->assertJsonValidationErrors('revision');
        $appointment->doctorFacility->update(['membership_status' => 'inactive']);

        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('1.000'))->assertUnprocessable()->assertJsonValidationErrors('amount');

        $this->assertDatabaseEmpty('doctor_payments');
        $this->assertDatabaseEmpty('doctor_appointment_events');
    }

    #[TestWith(['2026-10-14 09:00'])]
    #[TestWith(['2026-10-16 09:00'])]
    public function test_queue_cannot_call_past_or_future_appointments(string $date): void
    {
        $appointment = $this->appointment(['starts_at' => CarbonImmutable::parse($date, config('thera.schedule_timezone'))->utc(), 'price_minor' => 0, 'arrived_at' => CarbonImmutable::now('UTC')]);

        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 1])->assertUnprocessable();

        $this->assertDatabaseEmpty('doctor_queue_entries');
    }

    public function test_zero_balance_without_arrival_and_partial_payment_do_not_claim_readiness(): void
    {
        $appointment = $this->appointment();
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('5.000'))->assertOk();
        $appointment->update(['arrived_at' => CarbonImmutable::now('UTC')]);

        $response = $this->get(route('doctor.appointments.index', ['view' => 'day']))->assertOk();

        $this->assertSame(0, $response->viewData('summary')['ready']);
        $this->assertSame(1, $response->viewData('summary')['waiting']);
        $response->assertSee('مدفوع جزئيًا')->assertSee('وصل — بانتظار التحصيل');
    }

    public function test_ledger_records_cannot_be_edited(): void
    {
        $appointment = $this->appointment();
        $payment = DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id]);

        $this->expectException(\LogicException::class);
        $payment->update(['amount_minor' => 1]);
    }

    public function test_ledger_records_cannot_be_deleted(): void
    {
        $appointment = $this->appointment();
        $payment = DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id]);

        $this->expectException(\LogicException::class);
        $payment->delete();
    }

    public function test_cancellation_keeps_collected_balance_without_creating_a_fee_or_automatic_refund(): void
    {
        $appointment = $this->appointment();
        $this->postJson(route('doctor.finance.collections.store', $appointment), $this->payload('10.000'))->assertOk();

        $this->patchJson(route('doctor.appointments.status', $appointment), ['action' => 'cancelled', 'revision' => 2, 'reason' => 'طلب المريض إلغاء الموعد'])->assertOk();

        $response = $this->get(route('doctor.finance.collections', ['date' => '2026-10-15']))->assertOk()->assertSee('له رصيد محفوظ');
        $this->assertSame(10000, $response->viewData('totals')['JOD']['paid']);
        $this->assertSame(0, $response->viewData('totals')['JOD']['remaining']);
        $this->assertDatabaseCount('doctor_payments', 1);
        $this->get(route('doctor.finance.collections', ['date' => '2026-10-15', 'payment_status' => 'open']))->assertOk()->assertViewHas('appointments', fn ($items) => $items->total() === 0);
    }

    /** @param array<string, mixed> $attributes */
    private function appointment(array $attributes = []): DoctorAppointment
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-15 08:00', config('thera.schedule_timezone')));
        $appointment = DoctorAppointment::factory()->create(array_replace(['starts_at' => CarbonImmutable::parse('2026-10-15 09:00', config('thera.schedule_timezone'))->utc(), 'ends_at' => CarbonImmutable::parse('2026-10-15 09:30', config('thera.schedule_timezone'))->utc(), 'price_minor' => 20125, 'currency' => 'JOD'], $attributes));
        $this->actingAs($appointment->doctor, 'doctor');

        return $appointment;
    }

    /** @return array<string, mixed> */
    private function payload(string $amount): array
    {
        return ['amount' => $amount, 'method' => 'cash', 'revision' => 1, 'request_key' => (string) Str::uuid()];
    }
}
