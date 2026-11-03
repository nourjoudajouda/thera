<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientDoctorConsent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientDoctorConsent>
 */
class PatientDoctorConsentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(), 'doctor_id' => Doctor::factory(), 'granted_by' => User::factory(), 'expires_at' => now()->addDays(30),
        ];
    }
}
