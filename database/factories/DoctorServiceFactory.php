<?php

namespace Database\Factories;

use App\Models\DoctorFacility;
use App\Models\DoctorService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorService>
 */
class DoctorServiceFactory extends Factory
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
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->sentence(),
            'price_minor' => 30000,
            'currency' => 'JOD',
            'duration_minutes' => 30,
            'instructions' => null,
            'is_active' => false,
        ];
    }
}
