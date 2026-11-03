<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPayment;
use App\Models\DoctorQueueEntry;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_admin_sessions_cannot_read_the_doctor_dashboard(): void
    {
        $this->get(route('doctor.dashboard'))->assertRedirect(route('doctor.login'));
        $this->actingAs(Admin::factory()->create(), 'admin')->get(route('doctor.dashboard'))->assertRedirect(route('doctor.login'));
    }

    public function test_new_doctor_sees_empty_real_counts_and_shared_workspace(): void
    {
        $this->freezeTime();
        $this->actingAs(Doctor::factory()->create(['name' => '<script>alert(1)</script>']), 'doctor')
            ->get(route('doctor.dashboard'))->assertOk()
            ->assertViewHas('counts', ['appointments' => 0, 'waiting' => 0, 'ready' => 0, 'reports' => 0, 'followups' => 0])
            ->assertSee('لا يوجد مرضى جاهزون الآن.')->assertSee('id="practice-page-state"', false)
            ->assertSee('doctor-dashboard.css')->assertSee('doctor-practice-ajax.js')
            ->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_today_uses_local_midnight_and_never_includes_other_doctors(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-31 12:00', 'Asia/Hebron'));
        $doctor = Doctor::factory()->create();
        foreach (['2026-10-30 23:59:59', '2026-10-31 00:00:00', '2026-10-31 23:59:59', '2026-11-01 00:00:00'] as $index => $time) {
            DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'patient_name' => 'boundary-'.$index, 'starts_at' => CarbonImmutable::parse($time, 'Asia/Hebron')->utc()]);
        }
        DoctorAppointment::factory()->create(['patient_name' => 'foreign-secret', 'starts_at' => now()]);
        $this->actingAs($doctor, 'doctor')->get(route('doctor.dashboard'))->assertOk()
            ->assertViewHas('counts', fn ($counts) => $counts['appointments'] === 2)
            ->assertDontSee('boundary-0')->assertDontSee('boundary-3')->assertDontSee('foreign-secret');
    }

    public function test_ready_and_waiting_follow_arrival_net_payments_call_and_visit_state(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-31 12:00', 'Asia/Hebron'));
        $doctor = Doctor::factory()->create();
        foreach (['not_arrived', 'unpaid', 'partial', 'ready', 'called', 'in_visit', 'cancelled', 'reversed'] as $state) {
            $appointment = DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'starts_at' => now(), 'patient_name' => $state, 'price_minor' => 20000, 'status' => $state === 'cancelled' ? 'cancelled' : 'confirmed', 'arrived_at' => $state === 'not_arrived' ? null : now()]);
            if (! in_array($state, ['not_arrived', 'unpaid'], true)) {
                DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id, 'amount_minor' => $state === 'partial' ? 10000 : 20000]);
            }
            if ($state === 'reversed') {
                DoctorPayment::factory()->create(['doctor_appointment_id' => $appointment->id, 'kind' => 'reversal', 'amount_minor' => -20000]);
            }
            if (in_array($state, ['called', 'in_visit'], true)) {
                DoctorQueueEntry::factory()->create(['doctor_appointment_id' => $appointment->id]);
            }
            if ($state === 'in_visit') {
                DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id]);
            }
        }
        $this->actingAs($doctor, 'doctor')->get(route('doctor.dashboard'))->assertOk()
            ->assertViewHas('counts', fn ($counts) => $counts['appointments'] === 8 && $counts['waiting'] === 3 && $counts['ready'] === 2)
            ->assertViewHas('ready', fn ($items) => $items->pluck('patient_name')->values()->all() === ['ready', 'called']);
    }

    public function test_only_original_sections_are_rendered_with_pending_report_counts(): void
    {
        $this->freezeTime();
        $report = DoctorExternalReport::factory()->create();
        DoctorExternalReport::factory()->create(['doctor_diagnostic_order_id' => $report->doctor_diagnostic_order_id, 'reviewed_at' => now()]);
        DoctorExternalReport::factory()->create();
        $this->actingAs($report->order->doctorVisit->doctor, 'doctor')->get(route('doctor.dashboard'))->assertOk()
            ->assertViewHas('counts', fn ($counts) => $counts['reports'] === 1)
            ->assertSee('جاهزون الآن')->assertSee('تنبيهات مهمة')->assertSee('إجراءات سريعة')->assertSee('متابعات قادمة')
            ->assertDontSee('مسودات تحتاج إكمال')->assertDontSee('زيارات تحتاج متابعة')->assertDontSee('كل مواعيد اليوم');
    }

    public function test_upcoming_followups_exclude_drafts_foreign_outside_window_and_superseded_editions(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-31 12:00', 'Asia/Hebron'));
        $visit = DoctorVisit::factory()->create();
        foreach (['2026-10-30', '2026-10-31', '2026-11-06', '2026-11-07'] as $date) {
            DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'followup', 'status' => 'approved', 'approved_at' => now(), 'content' => ['due_date' => $date]]);
        }
        DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'followup', 'content' => ['due_date' => '2026-11-01']]);
        DoctorReferral::factory()->create(['kind' => 'followup', 'status' => 'approved', 'approved_at' => now(), 'content' => ['due_date' => '2026-11-01']]);
        $previous = DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'followup', 'status' => 'approved', 'approved_at' => now(), 'content' => ['due_date' => '2026-11-01']]);
        DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id, 'kind' => 'followup', 'status' => 'approved', 'approved_at' => now(), 'content' => ['due_date' => '2026-11-02'], 'corrects_document_id' => $previous->id, 'document_version' => 2, 'correction_reason' => 'تصحيح موعد المتابعة']);
        $this->actingAs($visit->doctor, 'doctor')->get(route('doctor.dashboard'))->assertOk()
            ->assertViewHas('counts', fn ($counts) => $counts['followups'] === 3);
    }

    public function test_preview_limits_do_not_truncate_counts_and_ajax_refresh_reads_current_state(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-10-31 12:00', 'Asia/Hebron'));
        $doctor = Doctor::factory()->create();
        DoctorAppointment::factory()->count(10)->create(['doctor_id' => $doctor->id, 'starts_at' => now(), 'arrived_at' => now(), 'price_minor' => 0]);
        $this->actingAs($doctor, 'doctor')->get(route('doctor.dashboard'))->assertOk()
            ->assertViewHas('counts', fn ($counts) => $counts['appointments'] === 10 && $counts['ready'] === 10)
            ->assertViewHas('ready', fn ($items) => $items->count() === 6);
        DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'starts_at' => now()]);
        $this->get(route('doctor.dashboard'), ['X-Requested-With' => 'XMLHttpRequest'])->assertOk()
            ->assertViewHas('counts', fn ($counts) => $counts['appointments'] === 11)->assertSee('id="main-content-layout"', false);
    }
}
