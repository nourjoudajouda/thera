<?php

namespace Database\Seeders;

use App\Models\DoctorPayment;
use Illuminate\Database\Seeder;

class DoctorPaymentSeeder extends Seeder
{
    /**
     * Create an isolated ledger fixture, never a receipt in a working environment.
     */
    public function run(): void
    {
        if (! app()->environment('testing') || config('database.default') !== 'sqlite') {
            throw new \LogicException('هذا الـSeeder مخصص لقاعدة SQLite معزولة في بيئة testing فقط.');
        }
        DoctorPayment::factory()->create();
    }
}
