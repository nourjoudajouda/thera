<?php

namespace Database\Factories;

use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorDiagnosticOrder>
 */
class DoctorDiagnosticOrderFactory extends Factory
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
            'kind' => 'lab', 'status' => 'draft', 'priority' => 'normal',
            'content' => ['items' => []], 'revision' => 1, 'request_key' => fake()->uuid(),
        ];
    }
}
