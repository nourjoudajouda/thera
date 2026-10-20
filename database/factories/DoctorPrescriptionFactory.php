<?php

namespace Database\Factories;

use App\Models\DoctorPrescription;
use App\Models\DoctorVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorPrescription>
 */
class DoctorPrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_visit_id' => DoctorVisit::factory(),
            'doctor_id' => fn (array $attributes) => DoctorVisit::findOrFail($attributes['doctor_visit_id'])->doctor_id,
            'status' => 'draft',
            'content' => ['items' => [], 'notes' => null],
            'revision' => 1,
        ];
    }
}
