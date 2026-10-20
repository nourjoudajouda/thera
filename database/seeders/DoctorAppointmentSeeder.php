<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorAppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $doctor = Doctor::query()->where('email', 'services-demo@thera.test')->first();
        if (! $doctor) {
            return;
        }
        DB::transaction(function () use ($doctor): void {
            Doctor::query()->lockForUpdate()->findOrFail($doctor->id);
            $key = 'c1656547-4410-4d43-8b96-4bd9e2999001';
            if (DoctorAppointment::query()->whereBelongsTo($doctor)->where('request_key', $key)->exists()) {
                return;
            }
            $services = DoctorService::query()->whereHas('doctorFacility', fn ($query) => $query->whereBelongsTo($doctor))->bookingEnabled()->with('doctorFacility.schedule.exceptions')->get();
            foreach ($services as $service) {
                $schedule = $service->doctorFacility->schedule;
                if (! $schedule) {
                    continue;
                }
                for ($offset = 1; $offset <= 14; $offset++) {
                    $date = CarbonImmutable::today(config('thera.schedule_timezone'))->addDays($offset);
                    $slots = $schedule->preview($date, $service)['slots'];
                    if ($slots === []) {
                        continue;
                    }
                    $start = CarbonImmutable::parse($date->toDateString().' '.$slots[0], config('thera.schedule_timezone'))->utc();
                    $appointment = DoctorAppointment::query()->create([
                        'doctor_id' => $doctor->id, 'doctor_facility_id' => $service->doctor_facility_id, 'doctor_service_id' => $service->id,
                        'patient_name' => 'مراجع تجريبي للمواعيد', 'patient_phone' => '+962790000000', 'notes' => 'بيانات تجريبية فقط، وليست حجزًا حقيقيًا.',
                        'service_name' => $service->name, 'price_minor' => $service->price_minor, 'currency' => $service->currency, 'duration_minutes' => $service->duration_minutes,
                        'starts_at' => $start, 'ends_at' => $start->addMinutes($service->duration_minutes), 'status' => 'confirmed', 'source' => 'doctor', 'revision' => 1,
                        'request_key' => $key, 'request_fingerprint' => hash('sha256', 'demo-appointment'),
                    ]);
                    $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'created', 'after' => $appointment->auditSnapshot()]);

                    return;
                }
            }
        });
    }
}
