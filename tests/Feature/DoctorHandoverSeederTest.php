<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPatient;
use App\Models\DoctorVisit;
use Database\Seeders\SpecialtySeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class DoctorHandoverSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_does_not_delete_or_change_accounts_or_create_a_backup(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified']);
        $other = Doctor::factory()->create();
        $before = $doctor->fresh()->getAttributes();

        $this->artisan('thera:prepare-handover', ['--date' => '2026-11-02'])->assertSuccessful();

        $this->assertSame($before, $doctor->fresh()->getAttributes());
        $this->assertModelExists($other);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_full_reset_preserves_only_verified_doctor_login_backs_up_old_rows_and_seeds_consistent_two_weeks(): void
    {
        Storage::fake('local');
        $this->travelTo(now()->setDate(2026, 11, 2)->setTime(22, 0));
        $doctor = Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified']);
        $login = $doctor->only(['id', 'email', 'password', 'phone', 'remember_token', 'verification_status']);
        $old = DoctorAppointment::factory()->create();
        $admin = Admin::factory()->create();
        Mail::fake();
        Notification::fake();

        $this->artisan('thera:prepare-handover', ['--apply' => true, '--date' => '2026-11-02'])->assertSuccessful();

        $this->assertSame($login, $doctor->fresh()->only(array_keys($login)));
        $this->assertDatabaseCount('doctors', 1);
        $this->assertModelMissing($admin);
        $this->assertModelMissing($old);
        $paths = Storage::disk('local')->files('handover-backups');
        $this->assertCount(1, $paths);
        $backup = json_decode(Storage::disk('local')->get($paths[0]), true, flags: JSON_THROW_ON_ERROR);
        $this->assertCount(2, $backup['tables']['doctors']);
        $this->assertSame($old->id, $backup['tables']['doctor_appointments'][0]['id']);
        $this->assertDatabaseCount('doctor_patients', 24);
        $this->assertDatabaseCount('patients', 12);
        $this->assertSame(0, DoctorAppointment::whereNull('doctor_patient_id')->count());
        $future = DoctorAppointment::where('starts_at', '>=', '2026-11-03')->get();
        $this->assertGreaterThan(80, $future->count());
        $this->assertSame('2026-11-16', $future->max('starts_at')->setTimezone(config('thera.schedule_timezone'))->toDateString());
        $this->assertSame(0, $future->whereNotNull('arrived_at')->count());
        $this->assertSame(0, $future->where('status', 'completed')->count());
        $this->assertSame(0, DoctorVisit::whereHas('doctorAppointment', fn ($q) => $q->where('starts_at', '>', now()))->count());
        $this->assertSame(1, DoctorVisit::whereNotNull('closed_at')->count());
        $this->assertSame(2, DoctorExternalReport::count());
        foreach (DoctorExternalReport::all() as $report) {
            Storage::disk('local')->assertExists($report->file_path);
        }
        $this->assertSame(2, $doctor->stamps()->count());
        $this->assertSame(2, $doctor->facilityMemberships()->count());
        $this->assertSame(2, $doctor->credentials()->count());
        $this->assertSame(2, $doctor->achievements()->count());
        $this->assertTrue($future->contains('source', 'patient_app'));
        $this->assertTrue($future->contains('source', 'doctor'));
        $this->assertSame(0, DoctorPatient::where('name', 'like', '%تجريبي%')->count());
        $this->actingAs($doctor->fresh(), 'doctor');
        foreach (['doctor.dashboard', 'doctor.account.professional-profile', 'doctor.account.settings', 'doctor.appointments.index', 'doctor.patients.index', 'doctor.clinical.visits', 'doctor.clinical.prescriptions', 'doctor.orders.lab', 'doctor.orders.radiology', 'doctor.orders.results', 'doctor.clinical.referrals', 'doctor.practice.services', 'doctor.practice.schedule', 'doctor.finance.collections'] as $route) {
            $this->get(route($route))->assertOk();
        }
        foreach (['month', 'week', 'list'] as $view) {
            $this->get(route('doctor.appointments.index', ['view' => $view, 'date' => '2026-11-03']))->assertOk();
        }
        $this->get(route('doctor.account.settings', ['tab' => 'stamps']))->assertOk()->assertSee('ختم العيادة')->assertSee('ختم المركز');
        $this->get(route('doctor.account.professional-profile', ['tab' => 'credentials']))->assertOk()->assertSee('البورد في طب الأسرة');
        Mail::assertNothingSent();
        Notification::assertNothingSent();
    }

    public function test_failed_rebuild_rolls_back_all_deletions_but_keeps_verified_backup(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified']);
        $old = DoctorAppointment::factory()->create();
        $before = $doctor->fresh()->getAttributes();
        $this->partialMock(SpecialtySeeder::class, fn ($mock) => $mock->shouldReceive('run')->once()->andThrow(new RuntimeException('Seed failure')));

        try {
            $this->artisan('thera:prepare-handover', ['--apply' => true, '--date' => '2026-11-02'])->run();
            $this->fail('Expected the rebuild to fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Seed failure', $exception->getMessage());
        }

        $this->assertSame($before, $doctor->fresh()->getAttributes());
        $this->assertModelExists($old);
        $this->assertDatabaseCount('doctor_appointments', 1);
        $this->assertCount(1, Storage::disk('local')->files('handover-backups'));
    }

    public function test_unverified_doctor_cannot_trigger_a_reset(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'profile_incomplete']);
        $this->expectException(ModelNotFoundException::class);

        $this->artisan('thera:prepare-handover', ['--apply' => true])->run();
    }

    public function test_production_is_refused_before_any_write(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified']);
        $this->app['env'] = 'production';

        try {
            $this->artisan('thera:prepare-handover', ['--apply' => true])->run();
            $this->fail('Production must be refused.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Handover reset is restricted to local and testing.', $exception->getMessage());
        }

        $this->assertModelExists($doctor);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_failed_backup_does_not_delete_any_data(): void
    {
        Doctor::factory()->create(['email' => 'doctor@thera.test', 'verification_status' => 'verified']);
        $old = DoctorAppointment::factory()->create();
        $disk = \Mockery::mock(FilesystemAdapter::class);
        $disk->shouldReceive('put')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('local')->once()->andReturn($disk);

        try {
            $this->artisan('thera:prepare-handover', ['--apply' => true])->run();
            $this->fail('Failed backup must stop deletion.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Backup verification failed. Nothing was deleted.', $exception->getMessage());
        }

        $this->assertModelExists($old);
        $this->assertDatabaseCount('doctors', 2);
    }
}
