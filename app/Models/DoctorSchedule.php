<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\DoctorScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['doctor_facility_id', 'periods', 'daily_capacity', 'is_active', 'revision'])]
class DoctorSchedule extends Model
{
    /** @use HasFactory<DoctorScheduleFactory> */
    use HasFactory;

    public const DAYS = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

    public function doctorFacility(): BelongsTo
    {
        return $this->belongsTo(DoctorFacility::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(DoctorScheduleException::class);
    }

    /** @return list<array<string, mixed>> */
    public function periodsForDate(CarbonImmutable $date): array
    {
        $exception = $this->exceptions->first(fn ($exception) => $exception->date->toDateString() === $date->toDateString());
        if ($exception) {
            return $exception->type === DoctorScheduleException::TYPE_SPECIAL ? $exception->periods : [];
        }

        return array_values(array_filter($this->periods ?? [], fn (array $period) => (int) $period['weekday'] === $date->dayOfWeek));
    }

    public static function minutes(string $time): int
    {
        [$hour, $minute] = explode(':', $time);

        return (int) $hour * 60 + (int) $minute;
    }

    /** @param array<string, mixed> $period
     * @return list<array{0: int, 1: int}>
     */
    public static function windows(array $period): array
    {
        $cursor = self::minutes($period['start']);
        $breaks = $period['breaks'] ?? [];
        usort($breaks, fn (array $a, array $b) => strcmp($a['start'], $b['start']));
        $windows = [];
        foreach ($breaks as $break) {
            $start = self::minutes($break['start']);
            if ($start > $cursor) {
                $windows[] = [$cursor, $start];
            }
            $cursor = self::minutes($break['end']);
        }
        $end = self::minutes($period['end']);
        if ($end > $cursor) {
            $windows[] = [$cursor, $end];
        }

        return $windows;
    }

    /** @return array{status: string, slots: list<string>, capacity: int, note: string} */
    public function preview(CarbonImmutable $date, DoctorService $service, ?int $ignoreAppointmentId = null): array
    {
        $result = ['status' => 'غير متاح', 'slots' => [], 'capacity' => $this->daily_capacity, 'note' => ''];
        $membership = $service->doctorFacility;
        $missing = [];
        if (! $this->is_active) {
            $missing[] = 'فعّل الجدول ثم اضغط حفظ الجدول.';
        }
        if ($this->daily_capacity === 0) {
            $missing[] = 'أدخل سعة يومية أكبر من صفر ثم احفظ الجدول.';
        }
        if (! $service->is_active) {
            $missing[] = 'فعّل الخدمة من صفحة الخدمات والأسعار.';
        }
        if ($membership->doctor->verification_status !== 'verified') {
            $missing[] = 'أكمل التحقق المهني للطبيب أولًا.';
        }
        if ($membership->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $membership->facility->is_active) {
            $missing[] = 'عضوية الطبيب والمنشأة يجب أن تكونا نشطتين.';
        }
        if (! $membership->facility->bookings_enabled) {
            $missing[] = 'حجوزات المنشأة غير مفعّلة؛ راجع إدارة المنشأة.';
        }
        if ($service->doctor_facility_id !== $this->doctor_facility_id) {
            $missing[] = 'اختر خدمة تابعة لمنشأة الجدول.';
        }
        if ($missing !== []) {
            $result['note'] = implode(' ', $missing);

            return $result;
        }
        $periods = $this->periodsForDate($date);
        if ($periods === []) {
            $result['status'] = 'عطلة';
            $result['note'] = 'لا يوجد دوام في هذا التاريخ، أو توجد إجازة أو فترة إغلاق. اختر يوم عمل آخر أو راجع الاستثناءات.';

            return $result;
        }
        $now = CarbonImmutable::now(config('thera.schedule_timezone'));
        $linked = false;
        $fits = false;
        foreach ($periods as $period) {
            if (! in_array($service->id, $period['service_ids'], true)) {
                continue;
            }
            $linked = true;
            foreach (self::windows($period) as [$start, $end]) {
                $fits = $fits || $end - $start >= $service->duration_minutes;
                for ($minute = $start; $minute + $service->duration_minutes <= $end; $minute += $service->duration_minutes) {
                    $time = sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60);
                    $instant = $date->setTime(intdiv($minute, 60), $minute % 60);
                    if ($instant->format('H:i') === $time && $instant->greaterThan($now)) {
                        $result['slots'][] = $time;
                    }
                }
            }
        }
        $appointments = DoctorAppointment::query()->where('doctor_id', $membership->doctor_id)->where('status', '!=', 'cancelled')
            ->when($ignoreAppointmentId, fn ($query) => $query->whereKeyNot($ignoreAppointmentId))
            ->where('starts_at', '<', $date->addDay()->startOfDay()->utc())
            ->where('ends_at', '>', $date->startOfDay()->utc())->get();
        $remaining = max(0, $this->daily_capacity - $appointments->where('doctor_facility_id', $this->doctor_facility_id)->count());
        $result['capacity'] = $remaining;
        $result['slots'] = array_values(array_filter($result['slots'], function (string $time) use ($appointments, $date, $service): bool {
            $start = CarbonImmutable::parse($date->toDateString().' '.$time, config('thera.schedule_timezone'))->utc();
            $end = $start->addMinutes($service->duration_minutes);

            return ! $appointments->contains(fn (DoctorAppointment $appointment) => $appointment->starts_at->lessThan($end) && $appointment->ends_at->greaterThan($start));
        }));
        sort($result['slots']);
        $result['status'] = $result['slots'] === [] ? 'ممتلئ' : (count($result['slots']) > $remaining ? 'محدود' : 'متاح');
        $result['note'] = 'تُخصم المواعيد المسجلة من الأوقات والسعة اليومية. اختيار الوقت لا يثبته؛ يُفحص التوفر مجددًا عند حفظ الموعد.';
        if (! $linked) {
            $result['note'] = 'الخدمة المختارة غير مرتبطة بفترات هذا اليوم. عدّل الفترة وأضف الخدمة، أو اختر خدمة مرتبطة بهذا اليوم.';
        } elseif (! $fits) {
            $result['note'] = 'مدة الخدمة أطول من الوقت المتصل المتاح بعد الاستراحات. وسّع الفترة أو اختر خدمة أقصر.';
        } elseif ($result['slots'] === []) {
            $result['status'] = $appointments->isEmpty() ? 'غير متاح' : 'ممتلئ';
            $result['note'] = $appointments->isEmpty() ? 'انتهت أوقات هذه الخدمة في التاريخ المختار. اختر تاريخًا لاحقًا أو أضف فترة عمل لاحقة.' : 'لا توجد أوقات متاحة بعد استبعاد المواعيد المسجلة. اختر تاريخًا آخر.';
        }
        if ($remaining === 0) {
            $result['slots'] = [];
            $result['status'] = 'ممتلئ';
            $result['note'] = 'اكتملت سعة الحجز اليومية لهذه المنشأة. اختر يومًا آخر.';
        }

        return $result;
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['periods' => 'array', 'daily_capacity' => 'integer', 'is_active' => 'boolean', 'revision' => 'integer'];
    }
}
