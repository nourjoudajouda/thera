<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorFacility;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorFacility>
 */
class DoctorFacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'facility_id' => Facility::factory(),
            'role' => 'طبيب',
            'membership_status' => DoctorFacility::STATUS_ACTIVE,
            'is_public' => true,
        ];
    }
}
