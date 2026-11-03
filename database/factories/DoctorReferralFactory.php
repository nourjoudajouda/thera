<?php

namespace Database\Factories;

use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DoctorReferral>
 */
class DoctorReferralFactory extends Factory
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
            'kind' => 'referral', 'status' => 'draft', 'priority' => 'normal', 'content' => [], 'revision' => 1, 'request_key' => (string) Str::uuid(),
        ];
    }
}
