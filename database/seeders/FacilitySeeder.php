<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            ['name' => 'مركز الشفاء الطبي', 'type' => 'medical_center', 'city' => 'عمّان', 'bookings_enabled' => true],
            ['name' => 'مستشفى المدينة التخصصي', 'type' => 'hospital', 'city' => 'عمّان', 'bookings_enabled' => false],
        ];

        foreach ($facilities as $facility) {
            Facility::query()->updateOrCreate(
                ['name' => $facility['name'], 'city' => $facility['city']],
                [...$facility, 'is_active' => true],
            );
        }
    }
}
