<?php

namespace Database\Factories;

use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleException;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorScheduleException>
 */
class DoctorScheduleExceptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_schedule_id' => DoctorSchedule::factory(),
            'date' => now()->addDay()->toDateString(),
            'type' => 'leave',
            'periods' => null,
            'note' => fake()->sentence(),
        ];
    }
}
