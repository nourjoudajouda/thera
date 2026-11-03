<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorReferral;
use App\Models\DoctorVisit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class ResetDoctorJourneySeeder extends Seeder
{
    public const TABLES = [
        'doctor_patient_events', 'doctor_external_reports', 'doctor_diagnostic_order_revisions', 'doctor_referral_revisions',
        'doctor_prescription_revisions', 'doctor_visit_revisions', 'doctor_appointment_events',
        'doctor_queue_entries', 'doctor_referrals', 'doctor_diagnostic_orders', 'doctor_prescriptions',
        'doctor_visits', 'doctor_payments', 'doctor_appointments', 'doctor_patients',
    ];

    public function run(bool $apply = false, ?string $date = null): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Journey resets are restricted to local and testing environments.');
        }
        $date ??= CarbonImmutable::today(config('thera.schedule_timezone'))->toDateString();
        Validator::make(['date' => $date], ['date' => ['required', 'date_format:Y-m-d']])->validate();
        $doctor = Doctor::where('email', 'doctor@thera.test')->firstOrFail();
        if ($doctor->verification_status !== 'verified') {
            throw new RuntimeException('The existing doctor must already be verified.');
        }
        $this->command?->table(['Journey table', 'Rows to replace'], collect(self::TABLES)->map(fn (string $table): array => [$table, DB::table($table)->count()])->all());
        if (! $apply) {
            $this->command?->info('Preview only. No records changed. Use --apply only after explicit approval.');

            return;
        }
        if (! extension_loaded('gd')) {
            throw new RuntimeException('GD is required to create readable report attachments. Nothing was deleted.');
        }
        DB::transaction(function () use ($date, $doctor): void {
            Doctor::query()->orderBy('id')->lockForUpdate()->get();
            $protected = [];
            foreach (['doctors', 'admins', 'facilities', 'doctor_facilities', 'doctor_services', 'doctor_schedules'] as $table) {
                $protected[$table] = hash('sha256', DB::table($table)->orderBy('id')->get()->toJson());
            }
            $tables = [];
            foreach (self::TABLES as $table) {
                $tables[$table] = DB::table($table)->orderBy('id')->lockForUpdate()->get()->all();
            }
            $payload = json_encode(['version' => 1, 'database' => DB::connection()->getDatabaseName(), 'created_at' => now()->toIso8601String(), 'tables' => $tables, 'files' => 'Original private report files are retained in place.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $backup = 'journey-backups/'.now()->format('Ymd-His').'-'.Str::uuid().'.json';
            $disk = Storage::disk('local');
            if (! $disk->put($backup, $payload) || ! hash_equals(hash('sha256', $payload), hash('sha256', $disk->get($backup)))) {
                throw new RuntimeException('Backup verification failed. No journey data was deleted.');
            }
            $this->command?->info('Verified private backup: '.$disk->path($backup));

            foreach (['doctor_referrals' => 'corrects_document_id', 'doctor_diagnostic_orders' => 'corrects_document_id', 'doctor_prescriptions' => 'corrects_prescription_id', 'doctor_payments' => 'reversal_of_id'] as $table => $column) {
                DB::table($table)->whereNotNull($column)->update([$column => null]);
            }
            foreach (self::TABLES as $table) {
                DB::table($table)->delete();
            }
            $this->call(DoctorJourneySeeder::class, parameters: ['date' => $date]);
            $open = DoctorVisit::where('doctor_id', $doctor->id)->whereHas('doctorAppointment', fn ($query) => $query->where('patient_name', 'يوسف ناصر'))->sole();
            $pending = DoctorVisit::where('doctor_id', $doctor->id)->whereHas('doctorAppointment', fn ($query) => $query->where('patient_name', 'هبة عادل'))->sole();
            $completed = DoctorVisit::where('doctor_id', $doctor->id)->whereHas('doctorAppointment', fn ($query) => $query->where('patient_name', 'خالد سليم'))->sole();
            foreach ([$open, $pending, $completed] as $visit) {
                $this->referral($visit, 'followup', $date, (bool) $visit->closed_at);
            }
            $this->referral($open, 'referral', $date, false);
            $this->referral($completed, 'referral', $date, true);
            $this->report($pending, 'Heba Adel', false, $date);
            $this->report($completed, 'Khaled Salim', true, $date);
            foreach ($protected as $table => $hash) {
                if (! hash_equals($hash, hash('sha256', DB::table($table)->orderBy('id')->get()->toJson()))) {
                    throw new RuntimeException('Protected accounts or practice settings changed. Reset rolled back: '.$table);
                }
            }
            $this->command?->info('Replaced journey records for '.$date.'. Accounts, profiles, memberships, services and schedules were preserved.');
        });
    }

    private function referral(DoctorVisit $visit, string $kind, string $date, bool $approved): void
    {
        $document = DoctorReferral::create([
            'doctor_id' => $visit->doctor_id, 'doctor_visit_id' => $visit->id, 'kind' => $kind,
            'request_key' => (string) Str::uuid(), 'status' => 'draft', 'priority' => 'normal', 'revision' => 1,
            'target_facility_id' => $kind === 'referral' ? $visit->doctorAppointment->doctorFacility->facility_id : null,
            'content' => ['title' => $kind === 'referral' ? 'تقييم ومتابعة سريرية' : 'مراجعة بعد استكمال الفحوصات', 'reason' => 'استكمال التقييم السريري ومراجعة الفحوصات المطلوبة.', 'clinical_summary' => $visit->content['assessment'], 'instructions' => 'إحضار التقارير وقائمة الأدوية الحالية عند المراجعة.', 'due_date' => $kind === 'followup' ? CarbonImmutable::parse($date)->addWeek()->toDateString() : null, 'followup_kind' => 'after_result', 'time_window' => 'morning'],
        ]);
        $document->revisions()->create(['doctor_id' => $visit->doctor_id, 'action' => 'created', 'revision' => 1, 'snapshot' => ['content' => $document->content, 'status' => 'draft']]);
        if ($approved) {
            $snapshot = $visit->diagnosticOrders()->whereNotNull('approved_at')->firstOrFail()->issued_snapshot;
            $snapshot['target_name'] = $kind === 'referral' ? $visit->doctorAppointment->doctorFacility->facility->name : null;
            $document->update(['status' => 'approved', 'approved_at' => now(), 'revision' => 2, 'issued_snapshot' => $snapshot]);
            $document->revisions()->create(['doctor_id' => $visit->doctor_id, 'action' => 'approved', 'revision' => 2, 'snapshot' => ['content' => $document->content, 'status' => 'approved', 'issued_snapshot' => $snapshot]]);
        }
    }

    private function report(DoctorVisit $visit, string $patient, bool $reviewed, string $date): void
    {
        $order = $visit->diagnosticOrders()->where('kind', 'lab')->whereNotNull('approved_at')->sole();
        $image = imagecreatetruecolor(900, 620);
        $white = imagecolorallocate($image, 255, 255, 255);
        $ink = imagecolorallocate($image, 30, 45, 65);
        $blue = imagecolorallocate($image, 0, 110, 170);
        imagefill($image, 0, 0, $white);
        $lines = ['AL ROWAD MEDICAL LABORATORY', 'Laboratory report', 'Patient: '.$patient, 'Report date: '.$date, 'Reference: RW-'.$date.'-'.$order->id, 'Requested tests: CBC, Ferritin', 'Haemoglobin: 13.4 g/dL', 'White blood cells: 6.2 x10^9/L', 'Platelets: 245 x10^9/L', 'Ferritin: 58 ng/mL', 'Please correlate with the clinical assessment.', 'External report - source not verified by THERA.'];
        foreach ($lines as $index => $line) {
            imagestring($image, 5, 42, 40 + $index * 43, $line, $index < 2 ? $blue : $ink);
        }
        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        imagedestroy($image);
        $path = 'doctors/'.$visit->doctor_id.'/external-reports/'.Str::uuid().'.png';
        if (! Storage::disk('local')->put($path, $bytes)) {
            throw new RuntimeException('Could not save report. The journey reset was rolled back.');
        }
        $report = $order->externalReports()->create(['doctor_id' => $visit->doctor_id, 'title' => 'تقرير صورة الدم ومخزون الحديد', 'source_name' => 'مختبر الرواد الطبي', 'source_reference' => 'RW-'.$date.'-'.$order->id, 'report_date' => $date, 'file_path' => $path, 'file_mime' => 'image/png', 'file_size' => strlen($bytes), 'file_sha256' => hash('sha256', $bytes), 'request_key' => (string) Str::uuid()]);
        $order->revisions()->create(['doctor_id' => $visit->doctor_id, 'action' => 'external_uploaded', 'revision' => $order->revision, 'snapshot' => ['external_report_id' => $report->id, 'source_name' => $report->source_name, 'file_sha256' => $report->file_sha256]]);
        if ($reviewed) {
            $report->update(['reviewed_at' => now(), 'review_note' => 'تم الاطلاع على التقرير وربطه بالتقييم السريري. تُناقش النتائج مع المراجع في المتابعة المحددة.']);
            $order->revisions()->create(['doctor_id' => $visit->doctor_id, 'action' => 'external_reviewed', 'revision' => $order->revision, 'snapshot' => ['external_report_id' => $report->id, 'review_note' => $report->review_note]]);
        }
    }
}
