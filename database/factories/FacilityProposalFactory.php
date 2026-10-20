<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\FacilityProposal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityProposal>
 */
class FacilityProposalFactory extends Factory
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
            'name' => fake()->company(),
            'type' => fake()->randomElement(['clinic', 'medical_center', 'hospital']),
            'city' => fake()->city(),
            'role' => 'طبيب',
            'status' => FacilityProposal::STATUS_PENDING,
            'rejection_reason' => null,
        ];
    }
}
