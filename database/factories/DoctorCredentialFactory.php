<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorCredential>
 */
class DoctorCredentialFactory extends Factory
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
            'type' => 'qualification',
            'title' => fake()->sentence(3),
            'issuer' => fake()->company(),
            'country' => fake()->country(),
            'issued_on' => fake()->dateTimeBetween('-15 years', '-1 year'),
            'expires_on' => null,
            'document_path' => 'doctor-documents/test/credential.pdf',
            'document_original_name' => 'credential.pdf',
            'verification_status' => DoctorCredential::STATUS_PENDING,
            'rejection_reason' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => ['verification_status' => DoctorCredential::STATUS_VERIFIED]);
    }
}
