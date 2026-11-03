<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use App\Models\DoctorPatient;
use App\Models\DoctorPayment;
use App\Models\DoctorService;
use App\Models\DoctorVisit;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;

class DoctorHandoverSeeder extends Seeder
{
    public const PROTECTED_PATIENT_TABLES = ['personal_access_tokens', 'patient_payment_attempts', 'patient_delegations', 'patient_doctor_consents', 'patient_access_events', 'patient_notifications', 'patient_devices', 'patient_reminders', 'patient_favorites', 'patient_reviews'];

    public const TABLES = [
        'patient_directory_events', ...ResetDoctorJourneySeeder::TABLES,
        'patients', 'doctor_schedule_exceptions', 'doctor_schedules',
        'doctor_services', 'doctor_facilities', 'facility_proposals', 'doctor_verification_requests',
        'doctor_credentials', 'doctor_achievements', 'doctor_specialties', 'doctor_stamps',
        'doctor_setting_events', 'medications', 'specialties', 'facilities', 'admins', 'users',
        'doctor_password_reset_tokens', 'password_reset_tokens', 'sessions', 'jobs', 'failed_jobs',
        'job_batches', 'cache_locks', 'cache',
    ];

    public function run(bool $apply = false, ?string $date = null): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Handover reset is restricted to local and testing.');
        }
        if (! app()->environment('testing') && (DB::connection()->getDriverName() !== 'mysql' || DB::connection()->getDatabaseName() !== 'thera')) {
            throw new RuntimeException('The local reset may only target the explicitly approved thera database.');
        }
        $date ??= CarbonImmutable::today(config('thera.schedule_timezone'))->toDateString();
        Validator::make(['date' => $date], ['date' => ['required', 'date_format:Y-m-d']])->validate();
        $today = CarbonImmutable::createFromFormat('!Y-m-d', $date, config('thera.schedule_timezone'));
        $doctor = Doctor::where('email', 'doctor@thera.test')->where('verification_status', 'verified')->firstOrFail();
        $schema = DB::connection()->getDriverName() === 'sqlite' ? 'main' : DB::connection()->getDatabaseName();
        $tables = Schema::getTableListing($schema, false);
        $expected = [...self::TABLES, ...self::PROTECTED_PATIENT_TABLES, 'doctors', 'migrations'];
        if (array_diff($tables, $expected) || array_diff($expected, $tables)) {
            throw new RuntimeException('Unexpected schema; review the exact reset table allowlist before deleting anything.');
        }
        $this->command?->table(['Table', 'Rows to replace'], collect(self::TABLES)->map(fn (string $table): array => [$table, DB::table($table)->count()])->all());
        $this->command?->info('Keep verified doctor '.$doctor->email.'; future dates '.$today->addDay()->toDateString().' through '.$today->addDays(14)->toDateString());
        if (! $apply) {
            return;
        }
        foreach (self::PROTECTED_PATIENT_TABLES as $table) {
            if (DB::table($table)->exists()) {
                throw new RuntimeException('Patient application data exists in '.$table.'. Handover reset is disabled to preserve it.');
            }
        }
        if (! extension_loaded('gd')) {
            throw new RuntimeException('GD is required. Nothing was deleted.');
        }
        DB::transaction(function () use ($doctor, $tables, $today): void {
            $doctor = Doctor::lockForUpdate()->findOrFail($doctor->id);
            $identity = $doctor->only(['id', 'email', 'phone', 'password', 'remember_token', 'verification_status']);
            $backup = [];
            foreach ($tables as $table) {
                $backup[$table] = DB::table($table)->lockForUpdate()->get()->all();
            }
            $payload = json_encode(['version' => 1, 'database' => DB::connection()->getDatabaseName(), 'created_at' => now()->toIso8601String(), 'tables' => $backup, 'files' => 'Original private uploads are retained in place; exclude backups and old uploads from delivery.'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $path = 'handover-backups/'.now()->format('Ymd-His').'-'.Str::uuid().'.json';
            $disk = Storage::disk('local');
            if (! $disk->put($path, $payload) || ! hash_equals(hash('sha256', $payload), hash('sha256', $disk->get($path)))) {
                throw new RuntimeException('Backup verification failed. Nothing was deleted.');
            }
            $this->command?->info('Verified private full backup: '.$disk->path($path));
            foreach (['doctor_referrals' => 'corrects_document_id', 'doctor_diagnostic_orders' => 'corrects_document_id', 'doctor_prescriptions' => 'corrects_prescription_id', 'doctor_payments' => 'reversal_of_id', 'specialties' => 'parent_id'] as $table => $column) {
                DB::table($table)->whereNotNull($column)->update([$column => null]);
            }
            foreach (self::TABLES as $table) {
                DB::table($table)->delete();
            }
            Doctor::whereKeyNot($doctor->id)->delete();
            $doctor->forceFill(['name' => 'د. أحمد خليل', 'languages' => ['العربية', 'الإنجليزية'],
                'professional_title' => 'أخصائي طب الأسرة', 'years_experience' => 12,
                'bio' => 'رعاية صحية شاملة للأسرة ومتابعة الأمراض المزمنة.',
                'professional_bio' => 'خبرة في طب الأسرة والرعاية الوقائية ومتابعة ضغط الدم والسكري، مع الاهتمام بتثقيف المرضى وخطط المتابعة.',
                'profile_completed_at' => now(), 'account_preferences' => null, 'preferences_revision' => 0, 'stamp_path' => null])->save();
            $this->profile($doctor, $today);
            $membership = $this->basePractice($doctor);
            $this->call(ResetDoctorJourneySeeder::class, parameters: ['apply' => true, 'date' => $today->toDateString()]);
            $this->practice($doctor, $membership, $today);
            $patients = $this->patients($doctor);
            foreach (DoctorAppointment::whereBelongsTo($doctor)->get() as $appointment) {
                $patient = $patients->firstWhere('name', $appointment->patient_name);
                $appointment->update(['doctor_patient_id' => $patient->id]);
                $patient->events()->create(['doctor_id' => $doctor->id, 'doctor_appointment_id' => $appointment->id, 'action' => 'appointment_linked', 'snapshot' => ['appointment_id' => $appointment->id]]);
            }
            $this->futureAppointments($doctor, $membership, $patients->all(), $today);
            foreach (DoctorVisit::whereBelongsTo($doctor)->whereNull('closed_at')->get() as $visit) {
                $visit->synchronizeWorkflowStatus();
            }
            if ($identity !== $doctor->fresh()->only(array_keys($identity)) || Doctor::count() !== 1) {
                throw new RuntimeException('Preserved login identity changed; reset rolled back.');
            }
            $this->command?->info('Handover ready: '.DoctorAppointment::count().' appointments, '.DoctorPatient::count().' patient files. No messages were sent.');
        });
    }

    private function profile(Doctor $doctor, CarbonImmutable $today): void
    {
        $this->call(SpecialtySeeder::class);
        foreach (['طب الأسرة' => 'primary', 'الرعاية الوقائية' => 'subspecialty'] as $name => $type) {
            $doctor->specialtyAssignments()->create(['specialty_id' => Specialty::where('name_ar', $name)->sole()->id, 'type' => $type, 'verification_status' => 'verified']);
        }
        $evidence = $this->image(['THERA', 'Professional profile record', 'Preview copy - not a legal credential']);
        $doctor->credentials()->create(['type' => 'board', 'title' => 'البورد في طب الأسرة', 'issuer' => 'المجلس الطبي', 'country' => 'الأردن', 'issued_on' => $today->subYears(8)->toDateString(), 'document_path' => $evidence, 'document_original_name' => 'professional-record.png', 'verification_status' => 'verified']);
        $doctor->credentials()->create(['type' => 'certificate', 'title' => 'دورة الإنعاش القلبي الرئوي', 'issuer' => 'مركز التعليم الطبي المستمر', 'country' => 'الأردن', 'issued_on' => $today->subMonths(3)->toDateString(), 'expires_on' => $today->addYear()->toDateString(), 'document_path' => $evidence, 'document_original_name' => 'training-record.png', 'verification_status' => 'pending']);
        foreach (['award' => 'التميز في الرعاية الأولية', 'accreditation' => 'اعتماد برنامج الرعاية الوقائية'] as $type => $title) {
            $doctor->achievements()->create(['type' => $type, 'title' => $title, 'organization' => 'جمعية طب الأسرة', 'year' => $today->year - 1, 'description' => 'المساهمة في تحسين تجربة المرضى وبرامج المتابعة والرعاية الوقائية.', 'evidence_path' => $evidence, 'evidence_original_name' => 'professional-achievement.png', 'verification_status' => 'verified']);
        }
        $document = ['path' => $evidence, 'original_name' => 'professional-record.png', 'mime_type' => 'image/png', 'size' => Storage::disk('local')->size($evidence), 'uploaded_at' => now()->toIso8601String()];
        $doctor->verificationRequests()->create(['reference' => 'VR-'.Str::upper(Str::random(10)), 'status' => 'approved', 'documents' => ['professional_license' => $document, 'primary_specialty_proof' => $document], 'profile_snapshot' => $doctor->only(['name', 'professional_title', 'years_experience']), 'submitted_at' => $today->subDays(3), 'decided_at' => $today->subDays(2)]);
        foreach (['ختم العيادة' => 'CLINIC', 'ختم المركز' => 'MEDICAL CENTER'] as $name => $label) {
            $path = $this->image(['THERA / '.$label, 'PREVIEW SEAL - NOT A SIGNATURE']);
            $doctor->stamps()->create(['name' => $name, 'path' => $path]);
        }
        $doctor->forceFill(['stamp_path' => $doctor->stamps()->firstOrFail()->path])->save();
    }

    private function practice(Doctor $doctor, DoctorFacility $membership, CarbonImmutable $today): void
    {
        $membership->update(['is_public' => true]);
        $membership->services()->create(['name' => 'تقييم دوري للأمراض المزمنة', 'description' => 'مراجعة الخطة العلاجية ونتائج المتابعة.', 'price_minor' => 30000, 'currency' => 'JOD', 'duration_minutes' => 30, 'is_active' => true]);
        $membership->services()->create(['name' => 'استشارة منزلية', 'description' => 'غير متاحة للحجز حاليًا.', 'price_minor' => 40000, 'currency' => 'JOD', 'duration_minutes' => 60, 'is_active' => false]);
        $schedule = $membership->schedule;
        $schedule->update(['periods' => collect([0, 1, 2, 3, 4, 6])->map(fn (int $day): array => ['weekday' => $day, 'start' => '09:00', 'end' => '17:00', 'breaks' => [['start' => '13:00', 'end' => '13:30']], 'service_ids' => $membership->services()->where('is_active', true)->pluck('id')->all()])->all(), 'daily_capacity' => 12]);
        $special = $today->addDays(9);
        if ($special->dayOfWeek === 5) {
            $special = $special->addDay();
        }
        $schedule->exceptions()->create(['date' => $special, 'type' => 'special_hours', 'periods' => [['start' => '09:00', 'end' => '13:00', 'breaks' => [], 'service_ids' => $membership->services()->where('is_active', true)->pluck('id')->all()]], 'note' => 'دوام صباحي فقط.']);
        $second = Facility::create(['name' => 'مركز الشفاء الطبي', 'type' => 'medical_center', 'city' => 'عمّان', 'bookings_enabled' => true, 'is_active' => true]);
        $secondMembership = $doctor->facilityMemberships()->create(['facility_id' => $second->id, 'role' => 'أخصائي طب الأسرة', 'membership_status' => 'active', 'is_public' => true]);
        $service = $secondMembership->services()->create(['name' => 'متابعة مسائية', 'description' => 'مراجعة دورية في المركز.', 'price_minor' => 20000, 'currency' => 'JOD', 'duration_minutes' => 30, 'is_active' => true]);
        $secondMembership->schedule()->create(['periods' => collect([0, 2, 4])->map(fn (int $day): array => ['weekday' => $day, 'start' => '18:00', 'end' => '20:00', 'breaks' => [], 'service_ids' => [$service->id]])->all(), 'daily_capacity' => 4, 'is_active' => true, 'revision' => 1]);
        $doctor->forceFill(['account_preferences' => ['preferred_membership_id' => $membership->id, 'availability' => 'available', 'require_collection_before_visit' => true, 'payment_policy' => ['type' => 'percentage', 'deposit_percent' => 20, 'remaining_timing' => 'before_visit', 'allow_early_payment' => true]]])->save();
    }

    private function basePractice(Doctor $doctor): DoctorFacility
    {
        $facility = Facility::create(['name' => 'عيادة الرواد للطب العام', 'city' => 'عمّان', 'type' => 'clinic', 'is_active' => true, 'bookings_enabled' => true]);
        $membership = $doctor->facilityMemberships()->create(['facility_id' => $facility->id, 'role' => 'طبيب', 'membership_status' => 'active', 'is_public' => true]);
        foreach (['كشف عام' => 25000, 'متابعة طبية' => 15000] as $name => $price) {
            $membership->services()->create(['name' => $name, 'description' => 'استشارة ومتابعة في عيادة الطب العام.', 'price_minor' => $price, 'duration_minutes' => 30, 'currency' => 'JOD', 'is_active' => true]);
        }
        $ids = $membership->services()->pluck('id')->all();
        $membership->schedule()->create(['periods' => collect([0, 1, 2, 3, 4, 6])->map(fn (int $day): array => ['weekday' => $day, 'start' => '09:00', 'end' => '17:00', 'breaks' => [['start' => '13:00', 'end' => '13:30']], 'service_ids' => $ids])->all(), 'daily_capacity' => 12, 'is_active' => true, 'revision' => 1]);

        return $membership;
    }

    /** @return Collection<int, DoctorPatient> */
    private function patients(Doctor $doctor): Collection
    {
        $names = ['ليلى محمود', 'أحمد منصور', 'سارة خليل', 'عمر حمدان', 'نور الشامي', 'يوسف ناصر', 'هبة عادل', 'خالد سليم', 'ريم صالح', 'محمود فؤاد', 'رنا إبراهيم', 'سامي يوسف', 'عبير عادل', 'لينا ناصر', 'رامي سلامة', 'هدى منصور', 'مالك حسن', 'منى خالد', 'إياد فؤاد', 'دانا أمين', 'مروان محمود', 'نسرين علي', 'حسن طارق', 'بسمة زياد'];

        return collect($names)->map(function (string $name, int $index) use ($doctor): DoctorPatient {
            $phone = '+962790'.str_pad((string) (8800 + $index), 6, '0', STR_PAD_LEFT);
            $network = null;
            if ($index % 2 === 0) {
                $user = User::create(['name' => $name, 'email' => 'patient'.($index + 1).'@thera.test', 'password' => Str::random(40)]);
                $network = Patient::create(['user_id' => $user->id, 'name' => $name, 'phone' => $phone, 'phone_verified_at' => now(), 'is_active' => true, 'booking_enabled' => true]);
            }
            $patient = DoctorPatient::create(['doctor_id' => $doctor->id, 'patient_id' => $network?->id, 'name' => $name, 'phone' => $phone]);
            $patient->events()->create(['doctor_id' => $doctor->id, 'action' => 'created', 'snapshot' => ['name' => $name, 'phone' => $phone, 'source' => $network ? 'thera' : 'local']]);

            return $patient;
        });
    }

    /** @param list<DoctorPatient> $patients */
    private function futureAppointments(Doctor $doctor, DoctorFacility $membership, array $patients, CarbonImmutable $today): void
    {
        $services = $membership->services()->where('is_active', true)->get();
        $schedule = $membership->schedule()->with('exceptions')->firstOrFail();
        $evening = $doctor->facilityMemberships()->whereKeyNot($membership->id)->with('services')->firstOrFail();
        for ($day = 1; $day <= 14; $day++) {
            $date = $today->addDays($day);
            if ($date->dayOfWeek === 5) {
                continue;
            }
            $special = $schedule->exceptions->contains(fn ($exception): bool => $exception->date->toDateString() === $date->toDateString());
            $slots = $special ? 6 : ($day === 3 ? 12 : 8);
            for ($slot = 0; $slot < $slots; $slot++) {
                $minutes = 9 * 60 + $slot * 30 + ($slot >= 8 ? 30 : 0);
                $start = $date->setTime(intdiv($minutes, 60), $minutes % 60)->utc();
                if (DoctorAppointment::whereBelongsTo($doctor)->where('starts_at', $start)->exists()) {
                    continue;
                }
                $patient = $patients[($day * 3 + $slot) % count($patients)];
                $service = $services[$slot % $services->count()];
                $appointment = $this->appointment($doctor, $membership, $service, $patient, $start, $slot === 7 && $day % 4 === 0);
                if ($slot < 2) {
                    $amount = $slot === 0 ? $appointment->price_minor : intdiv($appointment->price_minor, 5);
                    $payment = DoctorPayment::create(['doctor_id' => $doctor->id, 'doctor_appointment_id' => $appointment->id, 'kind' => 'payment', 'amount_minor' => $amount, 'currency' => 'JOD', 'method' => 'cash', 'request_key' => (string) Str::uuid(), 'request_fingerprint' => hash('sha256', (string) Str::uuid()), 'receipt_snapshot' => ['patient_name' => $patient->name, 'service_name' => $service->name, 'facility_name' => $membership->facility->name, 'starts_at' => $start->toIso8601String(), 'price_minor' => $appointment->price_minor, 'currency' => 'JOD']]);
                    $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'payment_recorded', 'after' => ['payment_id' => $payment->id, 'amount_minor' => $amount, 'paid_minor' => $amount]]);
                }
            }
            if (in_array($date->dayOfWeek, [0, 2, 4], true)) {
                foreach ($day === 3 ? [1080, 1110, 1140, 1170] : [1080, 1140] as $minutes) {
                    $this->appointment($doctor, $evening, $evening->services->first(), $patients[($day + $minutes) % count($patients)], $date->setTime(intdiv($minutes, 60), $minutes % 60)->utc());
                }
            }
        }
    }

    private function appointment(Doctor $doctor, DoctorFacility $membership, DoctorService $service, DoctorPatient $patient, CarbonImmutable $start, bool $cancelled = false): DoctorAppointment
    {
        $appointment = DoctorAppointment::create(['doctor_id' => $doctor->id, 'doctor_facility_id' => $membership->id, 'doctor_service_id' => $service->id, 'doctor_patient_id' => $patient->id,
            'patient_name' => $patient->name, 'patient_phone' => $patient->phone, 'service_name' => $service->name, 'price_minor' => $service->price_minor, 'currency' => $service->currency, 'duration_minutes' => $service->duration_minutes,
            'starts_at' => $start, 'ends_at' => $start->addMinutes($service->duration_minutes), 'status' => $cancelled ? 'cancelled' : 'confirmed', 'source' => $patient->patient_id ? 'patient_app' : 'doctor', 'revision' => 1,
            'request_key' => (string) Str::uuid(), 'request_fingerprint' => hash('sha256', (string) Str::uuid())]);
        $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'created', 'after' => $appointment->auditSnapshot()]);
        if ($cancelled) {
            $appointment->events()->create(['doctor_id' => $doctor->id, 'action' => 'cancelled', 'after' => $appointment->auditSnapshot()]);
        }

        return $appointment;
    }

    /** @param list<string> $lines */
    private function image(array $lines): string
    {
        $image = imagecreatetruecolor(700, 220);
        $white = imagecolorallocate($image, 255, 255, 255);
        $blue = imagecolorallocate($image, 15, 90, 155);
        imagefill($image, 0, 0, $white);
        foreach ($lines as $index => $line) {
            imagestring($image, 5, 30, 35 + $index * 50, $line, $blue);
        }
        ob_start();
        imagepng($image);
        $bytes = ob_get_clean();
        imagedestroy($image);
        $path = 'handover-documents/'.Str::uuid().'.png';
        if (! Storage::disk('local')->put($path, $bytes)) {
            throw new RuntimeException('Failed to save display document; reset rolled back.');
        }

        return $path;
    }
}
