<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class DoctorScheduleExceptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }
        $schedule = DoctorSchedule::query()->whereHas('doctorFacility.doctor', fn ($query) => $query->where('email', 'services-demo@thera.test'))->first();
        $schedule?->exceptions()->firstOrCreate(['date' => CarbonImmutable::today(config('thera.schedule_timezone'))->addDays(7)->toDateString()], ['type' => 'leave', 'note' => 'إجازة تجريبية لفحص الاستثناءات.']);
    }
}
