<?php

namespace Database\Seeders;

use App\Models\DoctorAppointment;
use App\Models\DoctorQueueEntry;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class DoctorQueueEntrySeeder extends Seeder
{
    /**
     * Create an isolated free-service queue fixture without inventing a payment.
     */
    public function run(): void
    {
        if (! app()->environment('testing') || config('database.default') !== 'sqlite') {
            throw new \LogicException('هذا الـSeeder مخصص لقاعدة SQLite معزولة في بيئة testing فقط.');
        }
        $start = CarbonImmutable::today(config('thera.schedule_timezone'))->setTime(9, 0)->utc();
        $appointment = DoctorAppointment::factory()->create(['price_minor' => 0, 'starts_at' => $start, 'ends_at' => $start->addMinutes(30), 'arrived_at' => $start]);
        DoctorQueueEntry::factory()->for($appointment)->create();
    }
}
