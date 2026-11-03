<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorPatient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorPatient>
 */
class DoctorPatientFactory extends Factory
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
            'name' => fake()->name(),
            'phone' => '+962790123456',
        ];
    }
}
