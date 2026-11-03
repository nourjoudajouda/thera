<?php

namespace Database\Factories;

use App\Models\DoctorAppointment;
use App\Models\PatientPaymentAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PatientPaymentAttempt>
 */
class PatientPaymentAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_appointment_id' => DoctorAppointment::factory(),
            'user_id' => User::factory(),
            'request_key' => (string) Str::uuid(),
            'amount_minor' => 3000,
            'status' => 'failed',
            'outcome' => 'failed',
        ];
    }
}
