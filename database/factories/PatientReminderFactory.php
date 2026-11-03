<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientReminder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientReminder>
 */
class PatientReminderFactory extends Factory
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
            'created_by' => fn (array $a) => Patient::findOrFail($a['patient_id'])->user_id,
            'title' => 'تذكير شخصي', 'time' => '09:00', 'timezone' => 'Asia/Hebron', 'enabled' => true,
        ];
    }
}
