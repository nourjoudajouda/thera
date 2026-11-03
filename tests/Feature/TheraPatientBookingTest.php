<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorPatient;
use App\Models\DoctorSchedule;
use App\Models\DoctorService;
use App\Models\DoctorVisit;
use App\Models\Patient;
use App\Models\PatientDirectoryEvent;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class TheraPatientBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_directory_requires_verified_doctor_and_exact_identifier_and_never_exposes_full_phone(): void
    {
        $patient = Patient::factory()->bookable()->create(['name' => 'ليلى محمود', 'phone' => '+962790111222']);
        $url = route('doctor.appointments.thera-patients', ['q' => $patient->phone]);
        $this->getJson($url)->assertUnauthorized();
        $this->actingAs(Admin::factory()->create(), 'admin')->getJson($url)->assertUnauthorized();
        $doctor = Doctor::factory()->create(['verification_status' => 'draft']);
        $this->actingAs($doctor, 'doctor')->getJson($url)->assertForbidden();
        $doctor->update(['verification_status' => 'verified']);
        foreach (['', 'ليلى', '790111', '%', (string) $patient->id] as $q) {
            $this->getJson(route('doctor.appointments.thera-patients', ['q' => $q]))->assertOk()->assertExactJson(['results' => []]);
        }
        foreach ([$patient->phone, $patient->reference()] as $q) {
            $this->getJson(route('doctor.appointments.thera-patients', ['q' => $q]))->assertOk()
                ->assertJsonPath('results.0.id', $patient->public_id)->assertJsonCount(1, 'results')->assertDontSee($patient->phone);
        }
        $this->assertSame(2, PatientDirectoryEvent::where('action', 'booking_lookup')->count());
    }

    public function test_booking_reuses_the_same_doctor_file_and_retries_do_not_create_duplicate_patients_or_events(): void
    {
        $service = $this->readyService();
        $patient = Patient::factory()->bookable()->create(['name' => 'سارة خليل', 'phone' => '+962790111222']);
        DoctorPatient::factory()->create(['doctor_id' => $service->doctorFacility->doctor_id, 'name' => $patient->name, 'phone' => $patient->phone]);
        $data = $this->payload($service, $patient) + ['patient_name' => 'اسم مزور', 'patient_phone' => '+962790999999'];
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();
        $appointment = DoctorAppointment::sole();
        $this->assertSame($patient->name, $appointment->patient_name);
        $this->assertSame($patient->phone, $appointment->patient_phone);
        $this->assertSame($patient->id, $appointment->patient->patient_id);
        $this->assertDatabaseCount('doctor_patients', 2);
        $this->assertDatabaseCount('patient_directory_events', 1);
        $data['request_key'] = (string) Str::uuid();
        $data['time'] = '09:30';
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();
        $this->assertDatabaseCount('doctor_patients', 2);
        $this->assertSame(1, DoctorAppointment::distinct()->count('doctor_patient_id'));
        $this->assertDatabaseCount('patient_directory_events', 2);
    }

    public function test_same_thera_patient_with_two_doctors_does_not_share_medical_records(): void
    {
        $patient = Patient::factory()->bookable()->create();
        $foreignFile = DoctorPatient::factory()->create(['patient_id' => $patient->id, 'name' => $patient->name, 'phone' => $patient->phone]);
        $foreignAppointment = DoctorAppointment::factory()->create(['doctor_id' => $foreignFile->doctor_id, 'doctor_patient_id' => $foreignFile->id]);
        DoctorVisit::factory()->create(['doctor_appointment_id' => $foreignAppointment->id]);
        $service = $this->readyService();
        $this->postJson(route('doctor.appointments.store'), $this->payload($service, $patient))->assertOk();
        $own = DoctorAppointment::where('doctor_id', $service->doctorFacility->doctor_id)->sole();
        $this->assertNotSame($foreignFile->id, $own->doctor_patient_id);
        $this->get(route('doctor.patients.show', ['patient' => $foreignFile->id]))->assertNotFound();
        $this->get(route('doctor.patients.show', ['patient' => $own->doctor_patient_id, 'tab' => 'visits']))->assertOk()->assertViewHas('records', fn ($records) => $records->isEmpty());
    }

    #[TestWith(['is_active', false])]
    #[TestWith(['booking_enabled', false])]
    #[TestWith(['phone_verified_at', null])]
    public function test_unavailable_patient_is_neither_discoverable_nor_bookable(string $field, mixed $value): void
    {
        $service = $this->readyService();
        $patient = Patient::factory()->bookable()->create([$field => $value]);
        $this->getJson(route('doctor.appointments.thera-patients', ['q' => $patient->reference()]))->assertOk()->assertExactJson(['results' => []]);
        $this->postJson(route('doctor.appointments.store'), $this->payload($service, $patient))->assertUnprocessable()->assertJsonValidationErrors('thera_patient_id');
        $this->assertDatabaseCount('doctor_patients', 0);
        $this->assertDatabaseCount('doctor_appointments', 0);
    }

    public function test_bad_patient_id_and_unavailable_time_do_not_leave_an_orphan_file(): void
    {
        $service = $this->readyService();
        $patient = Patient::factory()->bookable()->create();
        $data = $this->payload($service, $patient);
        $data['thera_patient_id'] = (string) Str::ulid();
        $this->postJson(route('doctor.appointments.store'), $data)->assertUnprocessable()->assertJsonValidationErrors('thera_patient_id');
        $data = $this->payload($service, $patient);
        $data['time'] = '03:00';
        $this->postJson(route('doctor.appointments.store'), $data)->assertUnprocessable();
        $this->assertDatabaseCount('doctor_patients', 0);
        $this->assertDatabaseCount('patient_directory_events', 0);
    }

    public function test_shared_phone_does_not_merge_distinct_registered_patients(): void
    {
        $service = $this->readyService();
        $first = Patient::factory()->bookable()->create(['phone' => '+962790111222']);
        $second = Patient::factory()->bookable()->create(['phone' => $first->phone]);
        $this->getJson(route('doctor.appointments.thera-patients', ['q' => $first->phone]))->assertOk()->assertJsonCount(2, 'results');
        $this->postJson(route('doctor.appointments.store'), $this->payload($service, $first))->assertOk();
        $data = $this->payload($service, $second);
        $data['time'] = '09:30';
        $this->postJson(route('doctor.appointments.store'), $data)->assertOk();
        $this->assertDatabaseCount('doctor_patients', 2);
        $this->assertSame(2, DoctorAppointment::distinct()->count('doctor_patient_id'));
    }

    private function readyService(): DoctorService
    {
        $this->travelTo(CarbonImmutable::parse('2026-11-01 08:00', config('thera.schedule_timezone')));
        $service = DoctorService::factory()->create(['is_active' => true, 'duration_minutes' => 30]);
        $service->doctorFacility->facility->update(['is_active' => true, 'bookings_enabled' => true]);
        $service->doctorFacility->doctor->update(['verification_status' => 'verified']);
        DoctorSchedule::factory()->create(['doctor_facility_id' => $service->doctor_facility_id, 'is_active' => true, 'daily_capacity' => 20, 'periods' => [['weekday' => 0, 'start' => '09:00', 'end' => '12:00', 'breaks' => [], 'service_ids' => [$service->id]]]]);
        $this->actingAs($service->doctorFacility->doctor, 'doctor');

        return $service;
    }

    /** @return array<string, mixed> */
    private function payload(DoctorService $service, Patient $patient): array
    {
        return ['doctor_service_id' => $service->id, 'date' => '2026-11-01', 'time' => '09:00', 'patient_mode' => 'thera', 'thera_patient_id' => $patient->public_id, 'request_key' => (string) Str::uuid()];
    }
}
