<?php

namespace Database\Factories;

use App\Models\PatientDevice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientDevice>
 */
class PatientDeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'personal_access_token_id' => fn (array $a) => User::findOrFail($a['user_id'])->createToken('test-device')->accessToken->id,
            'token' => fake()->uuid(), 'token_hash' => fn (array $a) => hash('sha256', $a['token']), 'platform' => 'android', 'locale' => 'ar', 'enabled' => true,
        ];
    }
}
