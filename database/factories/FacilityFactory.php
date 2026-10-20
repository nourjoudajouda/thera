<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => fake()->randomElement(['clinic', 'medical_center', 'hospital']),
            'city' => fake()->city(),
            'bookings_enabled' => fake()->boolean(),
            'is_active' => true,
        ];
    }
}
