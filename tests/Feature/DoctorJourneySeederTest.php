<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorPayment;
use App\Models\DoctorPrescription;
use App\Models\DoctorVisit;
use Database\Seeders\DoctorJourneySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DoctorJourneySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_journey_data_is_linked_repeatable_and_does_not_send_or_change_other_records(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 21)->setTime(16, 0));
        $doctor = Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified']);
        $existing = DoctorAppointment::factory()->create();
        $before = $existing->fresh()->getAttributes();
        Mail::fake();
        Notification::fake();

        $this->seed(DoctorJourneySeeder::class);

        $appointments = DoctorAppointment::query()->whereBelongsTo($doctor)->get();
        $this->assertCount(11, $appointments);
        $this->assertSame(6, DoctorPayment::query()->whereBelongsTo($doctor)->count());
        $this->assertSame(4, $appointments->filter(fn ($appointment) => $appointment->queueEntry()->exists())->count());
        $this->assertSame(3, DoctorVisit::query()->whereBelongsTo($doctor)->count());
        $this->assertSame(2, DoctorPrescription::query()->whereBelongsTo($doctor)->count());
        $this->assertSame(5, DoctorDiagnosticOrder::query()->whereBelongsTo($doctor)->count());
        $completed = $appointments->firstWhere('patient_name', 'خالد سليم');
        $this->assertSame('completed', $completed->status);
        $this->assertNotNull($completed->arrived_at);
        $this->assertNotNull($completed->visit->closed_at);
        $this->assertNotNull($completed->visit->prescription->approved_at);
        $this->assertSame('خالد سليم', $completed->visit->prescription->issued_snapshot['patient_name']);
        $open = $appointments->firstWhere('patient_name', 'يوسف ناصر');
        $this->assertNull($open->visit->closed_at);
        $this->assertNull($open->visit->prescription->approved_at);
        $this->assertSame(2, $open->visit->diagnosticOrders()->whereNull('approved_at')->count());
        $partial = $appointments->firstWhere('patient_name', 'سارة خليل');
        $this->assertSame(10000, (int) $partial->payments()->sum('amount_minor'));
        $this->assertGreaterThan(10000, $partial->price_minor);
        $this->assertDatabaseCount('medications', 0);
        $this->assertSame($before, $existing->fresh()->getAttributes());

        $open->update(['notes' => 'ملاحظة أضافها المستخدم']);
        $this->seed(DoctorJourneySeeder::class);

        $this->assertSame(11, DoctorAppointment::query()->whereBelongsTo($doctor)->count());
        $this->assertSame(6, DoctorPayment::query()->whereBelongsTo($doctor)->count());
        $this->assertSame(5, DoctorDiagnosticOrder::query()->whereBelongsTo($doctor)->count());
        $this->assertSame('ملاحظة أضافها المستخدم', $open->fresh()->notes);
        Mail::assertNothingSent();
        Notification::assertNothingSent();
        $this->actingAs($doctor, 'doctor')->get(route('doctor.clinical.visit', ['visit' => $open->visit->id]))->assertOk()->assertSee('يوسف ناصر')->assertSee('مراجعة وإغلاق الزيارة');
        $this->get(route('doctor.clinical.prescriptions.print', $completed->visit->prescription))->assertOk()->assertSee('محلول ملحي للأنف');
    }

    public function test_journey_seeder_refuses_production_without_creating_records(): void
    {
        app()->detectEnvironment(fn () => 'production');
        $this->expectException(\RuntimeException::class);
        try {
            app(DoctorJourneySeeder::class)->run();
        } finally {
            $this->assertDatabaseCount('doctor_appointments', 0);
            $this->assertDatabaseCount('facilities', 0);
        }
    }

    public function test_unverified_sample_doctor_is_not_silently_approved(): void
    {
        $doctor = Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'pending']);
        $this->expectException(\RuntimeException::class);
        try {
            $this->seed(DoctorJourneySeeder::class);
        } finally {
            $this->assertSame('pending', $doctor->fresh()->verification_status);
            $this->assertDatabaseCount('doctor_appointments', 0);
        }
    }
}
