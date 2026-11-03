<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPrescription;
use App\Models\DoctorReferral;
use Database\Seeders\DoctorJourneySeeder;
use Database\Seeders\ResetDoctorJourneySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResetDoctorJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_does_not_write_backup_delete_records_or_seed(): void
    {
        Storage::fake('local');
        Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified']);
        $appointment = DoctorAppointment::factory()->create();
        $before = $appointment->fresh()->getAttributes();
        $this->artisan('thera:reset-journey', ['--date' => '2026-10-31'])->assertSuccessful();
        $this->assertSame($before, $appointment->fresh()->getAttributes());
        $this->assertDatabaseCount('doctor_appointments', 1);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_reset_backs_up_all_journey_rows_preserves_settings_and_seeds_every_stage(): void
    {
        Storage::fake('local');
        $this->travelTo(now()->setDate(2026, 10, 31)->setTime(16, 0));
        $doctor = Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified']);
        $admin = Admin::factory()->create();
        $this->seed(DoctorJourneySeeder::class);
        $foreign = DoctorAppointment::factory()->create();
        $original = DoctorPrescription::whereNotNull('approved_at')->sole();
        DoctorPrescription::factory()->create(['doctor_id' => $doctor->id, 'doctor_visit_id' => $original->doctor_visit_id, 'document_version' => 2, 'corrects_prescription_id' => $original->id, 'correction_reason' => 'توضيح تعليمات الوصفة']);
        $oldReport = DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => DoctorDiagnosticOrder::whereNotNull('approved_at')->firstOrFail()->id]);
        Storage::disk('local')->put($oldReport->file_path, 'old file retained');
        $protected = [];
        foreach (['doctors', 'admins', 'facilities', 'doctor_facilities', 'doctor_services', 'doctor_schedules'] as $table) {
            $protected[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        $oldAppointmentIds = DoctorAppointment::pluck('id')->all();
        Mail::fake();
        Notification::fake();

        $this->artisan('thera:reset-journey', ['--apply' => true, '--date' => '2026-10-31'])->assertSuccessful();

        $backups = Storage::disk('local')->files('journey-backups');
        $this->assertCount(1, $backups);
        $backup = json_decode(Storage::disk('local')->get($backups[0]), true, flags: JSON_THROW_ON_ERROR);
        $this->assertCount(12, $backup['tables']['doctor_appointments']);
        $this->assertSame($oldReport->file_path, $backup['tables']['doctor_external_reports'][0]['file_path']);
        Storage::disk('local')->assertExists($oldReport->file_path);
        foreach ($protected as $table => $before) {
            $this->assertSame($before, DB::table($table)->orderBy('id')->get()->toJson(), $table.' changed');
        }
        $this->assertSame(0, DoctorAppointment::whereIn('id', $oldAppointmentIds)->count());
        $this->assertSame(0, DoctorAppointment::where('doctor_id', $foreign->doctor_id)->count());
        $this->assertDatabaseCount('doctor_appointments', 11);
        $this->assertDatabaseCount('doctor_external_reports', 2);
        $this->assertSame(1, DoctorExternalReport::whereNull('reviewed_at')->count());
        $this->assertSame(1, DoctorExternalReport::whereNotNull('reviewed_at')->count());
        $this->assertDatabaseCount('doctor_referrals', 5);
        $this->assertSame(2, DoctorReferral::whereNotNull('approved_at')->count());
        $this->assertSame(9, DoctorAppointment::whereDate('starts_at', '2026-10-31')->count());
        $this->assertSame(1, DoctorAppointment::whereDate('starts_at', '2026-10-29')->count());
        $this->assertSame(1, DoctorAppointment::whereDate('starts_at', '2026-11-01')->count());
        $this->actingAs($doctor, 'doctor');
        foreach (DoctorExternalReport::all() as $report) {
            $this->assertSame(hash('sha256', Storage::disk('local')->get($report->file_path)), $report->file_sha256);
            $this->get(route('doctor.orders.reports.show', $report))->assertOk()->assertSee('غير معتمد من THERA');
            $this->get(route('doctor.orders.reports.download', $report))->assertOk();
        }
        Mail::assertNothingSent();
        Notification::assertNothingSent();
        $this->assertSame($admin->email, $admin->fresh()->email);
    }

    public function test_failure_during_seeding_rolls_back_deletion_and_keeps_verified_backup(): void
    {
        Storage::fake('local');
        Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified', 'stamp_path' => 'missing-stamp.png']);
        $old = DoctorAppointment::factory()->create();
        $before = $old->fresh()->getAttributes();
        try {
            app(ResetDoctorJourneySeeder::class)->run(true, '2026-10-31');
            $this->fail('Expected missing stamp failure.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('stamp is missing', $exception->getMessage());
        }
        $this->assertSame($before, $old->fresh()->getAttributes());
        $this->assertDatabaseCount('doctor_appointments', 1);
        $this->assertCount(1, Storage::disk('local')->files('journey-backups'));
    }

    public function test_production_is_refused_before_backup_or_deletion(): void
    {
        Storage::fake('local');
        $appointment = DoctorAppointment::factory()->create();
        app()->detectEnvironment(fn () => 'production');
        try {
            app(ResetDoctorJourneySeeder::class)->run(true, '2026-10-31');
            $this->fail('Expected production refusal.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('restricted', $exception->getMessage());
        }
        $this->assertNotNull($appointment->fresh());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }
}
