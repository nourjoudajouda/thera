<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorSpecialty>
 */
class DoctorSpecialtyFactory extends Factory
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
            'specialty_id' => Specialty::factory(),
            'type' => 'primary',
            'verification_status' => 'pending',
            'rejection_reason' => null,
        ];
    }

    public function subspecialty(): static
    {
        return $this->state(fn (): array => ['type' => 'subspecialty']);
    }

    public function verified(): static
    {
        return $this->state(fn (): array => ['verification_status' => 'verified']);
    }
}
