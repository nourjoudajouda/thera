<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\PatientDelegation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientDelegation>
 */
class PatientDelegationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(), 'user_id' => User::factory(), 'kind' => 'adult', 'relationship' => 'family', 'status' => 'pending',
        ];
    }
}
