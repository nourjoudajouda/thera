<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        Patient::factory()->bookable()->count(3)->sequence(
            ['name' => 'مريم خالد', 'phone' => '+962790800101'],
            ['name' => 'أحمد سليم', 'phone' => '+962790800102'],
            ['name' => 'سارة عادل', 'phone' => '+962790800103'],
        )->create();
    }
}
