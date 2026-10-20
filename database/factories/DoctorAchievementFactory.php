<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorAchievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorAchievement>
 */
class DoctorAchievementFactory extends Factory
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
            'type' => 'award',
            'title' => fake()->sentence(4),
            'organization' => fake()->company(),
            'year' => fake()->numberBetween(1990, (int) date('Y')),
            'description' => fake()->paragraph(),
            'evidence_path' => 'doctor-documents/test/achievement.pdf',
            'evidence_original_name' => 'achievement.pdf',
            'verification_status' => DoctorAchievement::STATUS_PENDING,
            'rejection_reason' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => ['verification_status' => DoctorAchievement::STATUS_VERIFIED]);
    }
}
