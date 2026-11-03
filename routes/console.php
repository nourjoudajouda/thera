<?php

use Database\Seeders\DoctorHandoverSeeder;
use Database\Seeders\ResetDoctorJourneySeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('thera:reset-journey {--apply : Explicitly replace all local patient journey records} {--date= : Start date in Y-m-d format}', function (): void {
    app(ResetDoctorJourneySeeder::class)->setContainer(app())->setCommand($this)->__invoke(['apply' => (bool) $this->option('apply'), 'date' => $this->option('date')]);
})->purpose('Preview or reset local journey data with a verified private backup; preserve accounts and practice settings');

Artisan::command('thera:prepare-handover {--apply : Explicitly replace local data after a verified full backup} {--date= : Display base date in Y-m-d format}', function (): void {
    app(DoctorHandoverSeeder::class)->setContainer(app())->setCommand($this)->__invoke(['apply' => (bool) $this->option('apply'), 'date' => $this->option('date')]);
})->purpose('Rebuild the local handover data, preserve only the verified doctor login and seed the next fourteen days');
