<?php

namespace Database\Seeders;

use App\Models\DoctorService;
use Illuminate\Database\Seeder;

class DoctorScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $service = DoctorService::query()->whereHas('doctorFacility.doctor', fn ($query) => $query->where('email', 'services-demo@thera.test'))->first();
        if (! $service) {
            return;
        }
        $periods = [];
        foreach ([0, 1, 2, 3, 4, 6] as $day) {
            $periods[] = ['weekday' => $day, 'start' => '09:00', 'end' => '16:00', 'breaks' => [['start' => '12:00', 'end' => '12:30']], 'service_ids' => [$service->id]];
        }
        $service->doctorFacility->schedule()->firstOrCreate([], ['periods' => $periods, 'daily_capacity' => 10, 'is_active' => true, 'revision' => 1]);
    }
}
