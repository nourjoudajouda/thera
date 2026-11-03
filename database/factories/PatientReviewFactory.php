<?php

namespace Database\Factories;

use App\Models\DoctorAppointment;
use App\Models\DoctorPatient;
use App\Models\Patient;
use App\Models\PatientReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientReview>
 */
class PatientReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_appointment_id' => DoctorAppointment::factory()->state(['doctor_patient_id' => fn (array $a) => DoctorPatient::factory()->create(['doctor_id' => $a['doctor_id'], 'patient_id' => Patient::factory()])->id]),
            'patient_id' => fn (array $a) => DoctorAppointment::findOrFail($a['doctor_appointment_id'])->patient->patient_id,
            'user_id' => fn (array $a) => Patient::findOrFail($a['patient_id'])->user_id,
            'doctor_id' => fn (array $a) => DoctorAppointment::findOrFail($a['doctor_appointment_id'])->doctor_id,
            'rating' => 5, 'comment' => 'خدمة جيدة', 'status' => 'pending',
        ];
    }
}
