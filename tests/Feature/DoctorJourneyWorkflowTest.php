<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorPatient;
use App\Models\DoctorPrescription;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class DoctorJourneyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_arrival_collection_call_visit_documents_report_and_patient_history_form_one_journey(): void
    {
        Storage::fake('local');
        $this->travelTo(CarbonImmutable::parse('2026-10-31 08:00', 'Asia/Hebron'));
        $service = DoctorService::factory()->create(['price_minor' => 10000, 'currency' => 'JOD', 'is_active' => true, 'duration_minutes' => 30]);
        $membership = $service->doctorFacility;
        $membership->doctor->update(['verification_status' => 'verified']);
        $membership->facility->update(['is_active' => true, 'bookings_enabled' => true]);
        DoctorSchedule::factory()->create(['doctor_facility_id' => $membership->id, 'periods' => [['weekday' => 6, 'start' => '09:00', 'end' => '13:00', 'breaks' => [], 'service_ids' => [$service->id]]]]);
        $this->actingAs($membership->doctor->fresh(), 'doctor');

        $this->postJson(route('doctor.appointments.store'), ['doctor_service_id' => $service->id, 'date' => '2026-10-31', 'time' => '09:00', 'patient_mode' => 'new', 'patient_name' => 'مريم خليل', 'patient_phone' => '+962790111333', 'request_key' => (string) Str::uuid()])->assertOk();
        $appointment = DoctorAppointment::sole();
        $patient = DoctorPatient::sole();
        $this->assertSame($patient->id, $appointment->doctor_patient_id);
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 1])->assertUnprocessable();
        $this->patchJson(route('doctor.appointments.status', $appointment), ['revision' => 1, 'action' => 'arrived'])->assertOk();
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 2])->assertUnprocessable();
        $this->postJson(route('doctor.finance.collections.store', $appointment), ['revision' => 2, 'amount' => '10.000', 'method' => 'cash', 'request_key' => (string) Str::uuid()])->assertOk();
        $this->get(route('doctor.dashboard'))->assertOk()->assertViewHas('counts', fn ($counts) => $counts['ready'] === 1);
        $this->postJson(route('doctor.appointments.queue.store', $appointment), ['revision' => 3])->assertOk();
        $this->postJson(route('doctor.clinical.visits.start', $appointment), ['revision' => 4])->assertOk();
        $visit = DoctorVisit::sole();
        $this->get(route('doctor.dashboard'))->assertOk()->assertViewHas('counts', fn ($counts) => $counts['ready'] === 0);
        $this->putJson(route('doctor.clinical.visits.update', $visit), ['revision' => 1, 'intent' => 'save', 'status' => 'pending_results', 'complaint' => 'مراجعة الحالة العامة', 'assessment' => 'استكمال التقييم بعد الفحوصات'])->assertOk();

        $this->postJson(route('doctor.clinical.prescriptions.store', $visit), [])->assertOk();
        $rx = DoctorPrescription::sole();
        $this->putJson(route('doctor.clinical.prescriptions.update', $rx), ['revision' => 1, 'intent' => 'approve', 'review_confirmed' => 1, 'items' => [['source' => 'custom', 'name' => 'دواء خاص بالوصفة', 'active_ingredient' => 'مادة مدونة', 'strength' => 'تركيز مدون', 'dosage_form' => 'شكل مدون', 'dose' => 'جرعة مدونة', 'frequency' => 'تكرار مدون', 'duration' => 'مدة مدونة', 'quantity' => 'كمية مدونة']]])->assertOk();
        $this->assertDatabaseCount('medications', 0);
        $this->assertSame('مريم خليل', $rx->fresh()->issued_snapshot['patient_name']);

        $this->postJson(route('doctor.orders.store', [$visit, 'lab']), ['request_key' => (string) Str::uuid()])->assertOk();
        $order = DoctorDiagnosticOrder::sole();
        $this->putJson(route('doctor.orders.update', $order), ['revision' => 1, 'intent' => 'approve', 'review_confirmed' => 1, 'priority' => 'normal', 'clinical_context' => 'سياق سريري مدون', 'items' => [['name' => 'فحص خاص بهذا الطلب']]])->assertOk();
        $this->postJson(route('doctor.orders.reports.store', $order), ['title' => 'تقرير الفحص الخارجي', 'source_name' => 'مختبر الرواد', 'report_date' => '2026-10-31', 'identity_confirmed' => 1, 'request_key' => (string) Str::uuid(), 'report_file' => UploadedFile::fake()->image('report.png', 100, 100)])->assertOk();
        $report = DoctorExternalReport::sole();
        $this->get(route('doctor.dashboard'))->assertOk()->assertViewHas('counts', fn ($counts) => $counts['reports'] === 1);
        $this->postJson(route('doctor.orders.reports.review', $report), ['review_note' => 'تمت مراجعة التقرير وربطه بالتقييم السريري', 'review_confirmed' => 1])->assertOk();
        $this->get(route('doctor.orders.reports.show', $report))->assertOk()->assertSee('غير معتمد من THERA');
        $this->putJson(route('doctor.clinical.visits.update', $visit), ['revision' => 2, 'intent' => 'close', 'status' => 'pending_results', 'assessment' => 'تمت مراجعة التقرير وتوثيق التقييم النهائي'])->assertOk();

        $this->assertSame('completed', $appointment->fresh()->status);
        $this->assertNotNull($visit->fresh()->closed_at);
        $this->assertDatabaseCount('doctor_payments', 1);
        foreach (['appointments', 'visits', 'prescriptions', 'orders', 'reports'] as $tab) {
            $this->get(route('doctor.patients.show', ['patient' => $patient->id, 'tab' => $tab]))->assertOk()->assertSee('مريم خليل');
        }
        $this->get(route('doctor.patients.show', ['patient' => $patient->id, 'tab' => 'reports']))->assertOk()->assertSee('تقرير الفحص الخارجي');
        $this->get(route('doctor.dashboard'))->assertOk()->assertViewHas('counts', fn ($counts) => $counts['reports'] === 0 && $counts['ready'] === 0 && $counts['appointments'] === 1);
        $this->actingAs(Doctor::factory()->create(), 'doctor')->get(route('doctor.patients.show', ['patient' => $patient->id]))->assertNotFound();
        $this->get(route('doctor.clinical.visit', ['visit' => $visit->id]))->assertNotFound();
        $this->get(route('doctor.orders.reports.show', $report))->assertNotFound();
    }
}
