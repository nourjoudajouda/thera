<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\PatientFavorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientFavorite>
 */
class PatientFavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), 'doctor_id' => Doctor::factory(),
        ];
    }
}
