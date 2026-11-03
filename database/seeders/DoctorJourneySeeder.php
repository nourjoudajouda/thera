<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorFacility;
use App\Models\DoctorPayment;
use App\Models\DoctorPrescription;
use App\Models\DoctorQueueEntry;
use App\Models\DoctorVisit;
use App\Models\Facility;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class DoctorJourneySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(?string $date = null): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Journey fixtures are restricted to local and testing environments.');
        }
        $doctor = Doctor::query()->where('email', 'doctor@thera.test')->firstOrFail();
        if ($doctor->verification_status !== 'verified') {
            throw new RuntimeException('The sample doctor must already be verified.');
        }
        $rows = DB::transaction(function () use ($doctor, $date): array {
            Doctor::query()->lockForUpdate()->findOrFail($doctor->id);
            $facility = Facility::query()->firstOrCreate(['name' => 'عيادة الرواد للطب العام', 'city' => 'عمّان'], ['type' => 'clinic', 'is_active' => true, 'bookings_enabled' => true]);
            $membership = DoctorFacility::query()->firstOrCreate(['doctor_id' => $doctor->id, 'facility_id' => $facility->id], ['role' => 'طبيب', 'membership_status' => 'active', 'is_public' => false]);
            if (! $facility->is_active || ! $facility->bookings_enabled || $membership->membership_status !== 'active') {
                throw new RuntimeException('Existing inactive facility settings were not changed.');
            }
            $services = collect(['كشف عام' => 25000, 'متابعة طبية' => 15000])->map(fn (int $price, string $name) => $membership->services()->firstOrCreate(['name' => $name], ['description' => 'استشارة ومتابعة في عيادة الطب العام.', 'price_minor' => $price, 'duration_minutes' => 30, 'currency' => 'JOD', 'is_active' => true]));
            $periods = [];
            foreach ([0, 1, 2, 3, 4, 6] as $day) {
                $periods[] = ['weekday' => $day, 'start' => '09:00', 'end' => '17:00', 'breaks' => [['start' => '13:00', 'end' => '13:30']], 'service_ids' => $services->pluck('id')->all()];
            }
            $membership->schedule()->firstOrCreate([], ['periods' => $periods, 'daily_capacity' => 20, 'is_active' => true, 'revision' => 1]);
            $names = ['ليلى محمود', 'أحمد منصور', 'سارة خليل', 'عمر حمدان', 'نور الشامي', 'يوسف ناصر', 'هبة عادل', 'خالد سليم', 'ريم صالح', 'محمود فؤاد', 'رنا إبراهيم'];
            $stages = ['سجّلي الحضور', 'سجّلي التحصيل', 'أكملي التحصيل الجزئي', 'استدعي المريض', 'افتحي الزيارة', 'زيارة مفتوحة ومسودات', 'بانتظار النتائج — الطلبات غير مرسلة', 'زيارة مغلقة ومستندات معتمدة', 'ملغى', 'لم يحضر', 'موعد قادم'];
            $today = $date ? CarbonImmutable::createFromFormat('!Y-m-d', $date, config('thera.schedule_timezone')) : CarbonImmutable::today(config('thera.schedule_timezone'));
            $rows = [];
            foreach ($names as $index => $name) {
                $appointment = DoctorAppointment::query()->whereBelongsTo($doctor)->where('request_key', $this->key($index))->first();
                if (! $appointment) {
                    $date = $index === 10 ? $today->addDay() : ($index === 9 ? $today->subDay() : $today);
                    while ($date->dayOfWeek === 5) {
                        $date = $index === 9 ? $date->subDay() : $date->addDay();
                    }
                    $position = match ($index) {
                        5 => 0, 6 => 1, 7 => 2, 10 => 0, default => $index < 5 ? $index + 3 : $index
                    };
                    $start = $date->setTime(9, 0)->addMinutes($position * 30 + ($index >= 8 && $index < 10 ? 30 : 0))->utc();
                    $service = $services->get($index % 2 === 0 ? 'كشف عام' : 'متابعة طبية');
                    $appointment = DoctorAppointment::query()->create([
                        'doctor_id' => $doctor->id, 'doctor_facility_id' => $membership->id, 'doctor_service_id' => $service->id,
                        'patient_name' => $name, 'patient_phone' => '+962790'.str_pad((string) (8800 + $index), 6, '0', STR_PAD_LEFT),
                        'service_name' => $service->name, 'price_minor' => $service->price_minor, 'currency' => 'JOD', 'duration_minutes' => 30,
                        'starts_at' => $start, 'ends_at' => $start->addMinutes(30), 'status' => $index === 8 ? 'cancelled' : ($index === 9 ? 'no_show' : 'confirmed'),
                        'source' => 'doctor', 'revision' => 1, 'request_key' => $this->key($index), 'request_fingerprint' => hash('sha256', 'doctor-journey-v1-'.$index),
                        'arrived_at' => $index >= 1 && $index <= 7 ? $start->subMinutes(10)->min(CarbonImmutable::now('UTC')) : null,
                    ]);
                    $this->event($appointment, 'created', $appointment->auditSnapshot());
                    if ($appointment->arrived_at) {
                        $this->event($appointment, 'arrived', ['arrived_at' => $appointment->arrived_at->toIso8601String()]);
                    }
                    if ($index >= 2 && $index <= 7) {
                        $amount = $index === 2 ? 10000 : $appointment->price_minor;
                        $payment = DoctorPayment::query()->create(['doctor_id' => $doctor->id, 'doctor_appointment_id' => $appointment->id, 'kind' => 'payment', 'amount_minor' => $amount, 'currency' => 'JOD', 'method' => 'cash',
                            'request_key' => $this->key(100 + $index), 'request_fingerprint' => hash('sha256', 'doctor-journey-payment-'.$index),
                            'receipt_snapshot' => ['patient_name' => $name, 'service_name' => $service->name, 'facility_name' => $facility->name, 'starts_at' => $start->toIso8601String(), 'price_minor' => $service->price_minor, 'currency' => 'JOD']]);
                        $this->event($appointment, 'payment_recorded', ['payment_id' => $payment->id, 'amount_minor' => $amount, 'paid_minor' => $amount]);
                    }
                    if ($index >= 4 && $index <= 7) {
                        $calledAt = $start->min(CarbonImmutable::now('UTC'));
                        DoctorQueueEntry::query()->create(['doctor_id' => $doctor->id, 'doctor_appointment_id' => $appointment->id, 'called_at' => $calledAt]);
                        $this->event($appointment, 'patient_called', ['called_at' => $calledAt->toIso8601String()]);
                    }
                    if ($index >= 5 && $index <= 7) {
                        $this->visit($appointment, $index);
                    }
                    if (in_array($index, [8, 9], true)) {
                        $this->event($appointment, $appointment->status, $appointment->auditSnapshot());
                    }
                }
                $rows[] = [$appointment->id, $name, $appointment->starts_at->setTimezone(config('thera.schedule_timezone'))->format('Y-m-d H:i'), $stages[$index]];
            }

            return $rows;
        }, 3);
        $this->command?->table(['Appointment', 'Patient', 'Date', 'Starting scenario'], $rows);
    }

    private function key(int $index): string
    {
        return 'f4b9f4d0-0021-4026-8021-'.str_pad((string) $index, 12, '0', STR_PAD_LEFT);
    }

    /** @param array<string, mixed> $after */
    private function event(DoctorAppointment $appointment, string $action, array $after): void
    {
        $appointment->events()->create(['doctor_id' => $appointment->doctor_id, 'action' => $action, 'after' => $after + ['fixture' => 'doctor-journey-v1']]);
    }

    private function visit(DoctorAppointment $appointment, int $index): void
    {
        $content = ['complaint' => 'مراجعة للاطمئنان ومتابعة الحالة العامة.', 'medical_history' => 'تمت مراجعة التاريخ المرضي والأدوية الحالية مع المراجع.', 'physical_exam' => 'جرى الفحص السريري وتوثيق الملاحظات أثناء المقابلة.', 'clinical_notes' => 'نوقشت الأسئلة والتعليمات مع المراجع.',
            'assessment' => 'تقييم مبدئي للحالة العامة مع متابعة حسب تطور الأعراض.', 'diagnosis' => '', 'diagnosis_type' => 'initial', 'treatment_plan' => 'مراجعة التعليمات ومتابعة الحالة بعد استكمال التقييم.', 'followup_plan' => 'مراجعة بعد أسبوع أو قبل ذلك عند الحاجة.'];
        $visit = DoctorVisit::query()->create(['doctor_appointment_id' => $appointment->id, 'doctor_id' => $appointment->doctor_id, 'doctor_facility_id' => $appointment->doctor_facility_id, 'status' => $index === 6 ? 'pending_results' : 'draft', 'content' => $content, 'revision' => 1, 'started_at' => $appointment->starts_at->addMinutes(5)]);
        $this->visitRevision($visit, 'started');
        $this->event($appointment, 'visit_started', ['visit_id' => $visit->id]);
        if (in_array($index, [5, 7], true)) {
            $prescription = DoctorPrescription::query()->create(['doctor_id' => $appointment->doctor_id, 'doctor_visit_id' => $visit->id, 'status' => 'draft', 'revision' => 1, 'content' => ['items' => [[
                'source' => 'custom', 'medication_id' => null, 'name' => 'محلول ملحي للأنف', 'active_ingredient' => 'Sodium chloride', 'strength' => '0.9%', 'dosage_form' => 'بخاخ أنفي', 'dose' => 'بخة بكل فتحة أنف', 'frequency' => 'مرتين يوميًا عند الحاجة', 'duration' => 'ثلاثة أيام', 'quantity' => 'عبوة واحدة', 'instructions' => 'للاستخدام الأنفي فقط.',
            ]], 'notes' => 'تُراجع تعليمات الاستخدام مع الطبيب.']]);
            $this->documentRevision($prescription, 'created');
            if ($index === 7) {
                $prescription->update(['status' => 'approved', 'approved_at' => now('UTC'), 'issued_snapshot' => $this->snapshot($visit), 'revision' => 2]);
                $this->documentRevision($prescription, 'approved');
            }
        }
        foreach ($index === 7 ? ['lab'] : ['lab', 'radiology'] as $kind) {
            $order = DoctorDiagnosticOrder::query()->create(['doctor_id' => $appointment->doctor_id, 'doctor_visit_id' => $visit->id, 'kind' => $kind, 'status' => 'draft', 'priority' => 'normal', 'revision' => 1,
                'request_key' => $this->key(200 + $index * 2 + ($kind === 'lab' ? 0 : 1)),
                'content' => ['clinical_context' => 'استكمال التقييم السريري المدون في الزيارة.', 'clinical_question' => $kind === 'radiology' ? 'تقييم المنطقة المحددة بناءً على الفحص السريري.' : null, 'instructions' => null,
                    'items' => $kind === 'lab' ? [['name' => 'صورة دم كاملة (CBC)', 'instructions' => null], ['name' => 'مخزون الحديد (Ferritin)', 'instructions' => null]] : [['name' => 'أشعة سينية', 'body_area' => 'الركبة اليمنى', 'instructions' => null]]]]);
            $this->documentRevision($order, 'created');
            if ($index !== 5) {
                $orderContent = $order->content;
                $orderContent['items'] = array_map(fn (array $item): array => $item + ['source' => 'custom', 'id' => (string) Str::uuid()], $orderContent['items']);
                $order->update(['content' => $orderContent, 'status' => 'approved', 'approved_at' => now('UTC'), 'issued_snapshot' => $this->snapshot($visit), 'revision' => 2]);
                $this->documentRevision($order, 'approved');
            }
        }
        if ($index === 7) {
            $visit->update(['status' => 'completed', 'closed_at' => now('UTC'), 'revision' => 2]);
            $this->visitRevision($visit, 'completed');
            $appointment->update(['status' => 'completed', 'revision' => 2]);
            $this->event($appointment, 'visit_completed', $appointment->auditSnapshot());
        }
    }

    private function visitRevision(DoctorVisit $visit, string $action): void
    {
        $visit->revisions()->create(['doctor_id' => $visit->doctor_id, 'action' => $action, 'revision' => $visit->revision, 'snapshot' => ['content' => $visit->content, 'status' => $visit->status, 'fixture' => 'doctor-journey-v1']]);
    }

    private function documentRevision(DoctorPrescription|DoctorDiagnosticOrder $document, string $action): void
    {
        $document->revisions()->create(['doctor_id' => $document->doctor_id, 'action' => $action, 'revision' => $document->revision, 'snapshot' => ['content' => $document->content, 'status' => $document->status, 'issued_snapshot' => $document->issued_snapshot, 'fixture' => 'doctor-journey-v1']]);
    }

    /** @return array<string, mixed> */
    private function snapshot(DoctorVisit $visit): array
    {
        $doctor = $visit->doctor;
        $stamp = null;
        if ($doctor->stamp_path) {
            if (! Storage::disk('local')->exists($doctor->stamp_path)) {
                throw new RuntimeException('The stored stamp is missing. No journey records were committed.');
            }
            $bytes = Storage::disk('local')->get($doctor->stamp_path);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);
            if (! in_array($mime, ['image/png', 'image/jpeg'], true) || strlen($bytes) > 1048576) {
                throw new RuntimeException('The stored stamp is invalid. No journey records were committed.');
            }
            $stamp = 'data:'.$mime.';base64,'.base64_encode($bytes);
        }

        return ['doctor_name' => $doctor->name, 'professional_title' => $doctor->professional_title, 'specialty' => $doctor->primarySpecialtyAssignment()->where('verification_status', 'verified')->with('specialty')->first()?->specialty?->name_ar,
            'patient_name' => $visit->doctorAppointment->patient_name, 'patient_phone' => $visit->doctorAppointment->patient_phone, 'facility_name' => $visit->doctorAppointment->doctorFacility->facility->name,
            'visit_id' => $visit->id, 'appointment_id' => $visit->doctor_appointment_id, 'visit_date' => $visit->started_at->setTimezone(config('thera.schedule_timezone'))->format('Y/m/d H:i'),
            'stamp_data_uri' => $stamp, 'logo_data_uri' => 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('images/doctor/thera-logo.png'))), 'fixture' => 'doctor-journey-v1'];
    }
}
