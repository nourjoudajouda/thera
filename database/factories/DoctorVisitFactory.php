<?php

namespace Database\Factories;

use App\Models\DoctorAppointment;
use App\Models\DoctorVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorVisit>
 */
class DoctorVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_appointment_id' => DoctorAppointment::factory(),
            'doctor_id' => fn (array $attributes) => DoctorAppointment::findOrFail($attributes['doctor_appointment_id'])->doctor_id,
            'doctor_facility_id' => fn (array $attributes) => DoctorAppointment::findOrFail($attributes['doctor_appointment_id'])->doctor_facility_id,
            'status' => 'draft', 'content' => [], 'revision' => 1, 'started_at' => now(),
        ];
    }
}
