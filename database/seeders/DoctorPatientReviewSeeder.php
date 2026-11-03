<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorPatient;
use App\Models\DoctorPrescription;
use App\Models\DoctorReferral;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use App\Models\DoctorVisit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DoctorPatientReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) || DB::connection()->getDriverName() !== 'sqlite' || ! str_starts_with(basename(DB::connection()->getDatabaseName()), 'thera-patient-review-')) {
            throw new RuntimeException('Patient UI fixtures require a dedicated thera-patient-review-* SQLite database.');
        }
        if (Doctor::where('email', 'patient-review@thera.test')->exists()) {
            return;
        }
        DB::transaction(function (): void {
            $doctor = Doctor::factory()->create(['name' => 'د. أحمد خليل', 'email' => 'patient-review@thera.test', 'verification_status' => 'verified']);
            $patient = DoctorPatient::factory()->create(['doctor_id' => $doctor->id, 'name' => 'هبة عادل', 'phone' => '+962790111222']);
            $service = DoctorService::factory()->create(['is_active' => true, 'duration_minutes' => 30]);
            $service->doctorFacility->update(['doctor_id' => $doctor->id]);
            $service->doctorFacility->facility->update(['name' => 'مركز الشفاء الطبي', 'is_active' => true, 'bookings_enabled' => true]);
            DoctorSchedule::factory()->create(['doctor_facility_id' => $service->doctor_facility_id, 'daily_capacity' => 20, 'is_active' => true, 'periods' => collect(range(0, 6))->map(fn (int $day): array => ['weekday' => $day, 'start' => '08:00', 'end' => '18:00', 'breaks' => [], 'service_ids' => [$service->id]])->all()]);
            $appointment = DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'doctor_facility_id' => $service->doctor_facility_id, 'doctor_service_id' => $service->id, 'doctor_patient_id' => $patient->id, 'patient_name' => $patient->name, 'patient_phone' => $patient->phone, 'arrived_at' => now()->subHour(), 'starts_at' => now()->subHour(), 'ends_at' => now()->subMinutes(30)]);
            $visit = DoctorVisit::factory()->create(['doctor_appointment_id' => $appointment->id, 'status' => 'pending_results', 'content' => ['complaint' => 'مراجعة ومتابعة الحالة العامة.', 'assessment' => 'استكمال التقييم السريري بعد مراجعة الفحوصات المطلوبة.']]);
            DoctorPrescription::factory()->create(['doctor_visit_id' => $visit->id]);
            DoctorDiagnosticOrder::factory()->create(['doctor_visit_id' => $visit->id, 'status' => 'approved', 'approved_at' => now(), 'content' => ['items' => [['name' => 'صورة دم كاملة (CBC)']]], 'issued_snapshot' => ['patient_name' => $patient->name]]);
            DoctorReferral::factory()->create(['doctor_visit_id' => $visit->id]);
            $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'created', 'after' => $appointment->auditSnapshot()]);
            $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'arrived', 'after' => ['arrived_at' => $appointment->arrived_at->toIso8601String()]]);
            $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'visit_started', 'after' => ['visit_id' => $visit->id]]);
            DoctorAppointment::factory()->create(['doctor_id' => $doctor->id, 'doctor_facility_id' => $service->doctor_facility_id, 'doctor_service_id' => $service->id, 'patient_name' => 'خالد سليم']);
        });
    }
}
