<?php

namespace Database\Factories;

use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use App\Models\DoctorService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DoctorAppointment>
 */
class DoctorAppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_facility_id' => DoctorFacility::factory(),
            'doctor_id' => fn (array $attributes) => DoctorFacility::query()->findOrFail($attributes['doctor_facility_id'])->doctor_id,
            'doctor_service_id' => fn (array $attributes) => DoctorService::factory()->create(['doctor_facility_id' => $attributes['doctor_facility_id']])->id,
            'patient_name' => fake()->name(), 'patient_phone' => '+962790123456',
            'service_name' => 'استشارة', 'price_minor' => 20000, 'currency' => 'JOD', 'duration_minutes' => 30,
            'starts_at' => CarbonImmutable::tomorrow(config('thera.schedule_timezone'))->setTime(9, 0)->utc(),
            'ends_at' => CarbonImmutable::tomorrow(config('thera.schedule_timezone'))->setTime(9, 30)->utc(),
            'status' => 'confirmed', 'source' => 'doctor', 'revision' => 1,
            'request_key' => (string) Str::uuid(), 'request_fingerprint' => hash('sha256', (string) Str::uuid()),
        ];
    }
}
