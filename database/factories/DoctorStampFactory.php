<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorStamp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorStamp>
 */
class DoctorStampFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'name' => fake()->words(2, true),
            'path' => 'doctors/test/stamps/stamp.png',
        ];
    }
}
