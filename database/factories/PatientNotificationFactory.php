<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientNotification>
 */
class PatientNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'user_id' => fn (array $a) => Patient::findOrFail($a['patient_id'])->user_id,
            'event_key' => fake()->uuid(), 'kind' => 'appointment_updated', 'target_type' => 'appointment', 'target_id' => 1, 'push_state' => 'skipped',
        ];
    }
}
