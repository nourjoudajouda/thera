<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorVerificationRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorVerificationRequest>
 */
class DoctorVerificationRequestFactory extends Factory
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
            'status' => DoctorVerificationRequest::STATUS_DRAFT,
            'documents' => [],
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (): array => [
            'reference' => 'VR-'.now()->format('Y').'-'.fake()->unique()->numerify('######'),
            'status' => DoctorVerificationRequest::STATUS_SUBMITTED,
            'documents' => [
                DoctorVerificationRequest::DOCUMENT_PROFESSIONAL_LICENSE => [
                    'path' => 'doctor-verification-documents/license.pdf',
                    'original_name' => 'license.pdf',
                    'mime_type' => 'application/pdf',
                    'size' => 1024,
                    'uploaded_at' => now()->toISOString(),
                ],
                DoctorVerificationRequest::DOCUMENT_PRIMARY_SPECIALTY_PROOF => [
                    'path' => 'doctor-verification-documents/specialty.pdf',
                    'original_name' => 'specialty.pdf',
                    'mime_type' => 'application/pdf',
                    'size' => 1024,
                    'uploaded_at' => now()->toISOString(),
                ],
            ],
            'submitted_at' => now(),
        ]);
    }
}
