<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'phone' => '+96279'.fake()->numerify('#######'),
            'phone_verified_at' => null,
            'is_active' => true,
            'booking_enabled' => false,
        ];
    }

    public function bookable(): static
    {
        return $this->state(fn (array $attributes): array => ['phone_verified_at' => now(), 'booking_enabled' => true]);
    }
}
