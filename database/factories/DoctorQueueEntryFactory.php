<?php

namespace Database\Factories;

use App\Models\DoctorAppointment;
use App\Models\DoctorQueueEntry;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DoctorQueueEntry> */
class DoctorQueueEntryFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'doctor_appointment_id' => DoctorAppointment::factory(),
            'doctor_id' => fn (array $attributes) => DoctorAppointment::findOrFail($attributes['doctor_appointment_id'])->doctor_id,
            'called_at' => CarbonImmutable::now('UTC'),
        ];
    }
}
