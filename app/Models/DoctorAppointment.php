<?php

namespace App\Models;

use Database\Factories\DoctorAppointmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;

#[Fillable(['doctor_id', 'doctor_facility_id', 'doctor_service_id', 'patient_name', 'patient_phone', 'service_name', 'price_minor', 'currency', 'duration_minutes', 'starts_at', 'ends_at', 'status', 'source', 'arrived_at', 'notes', 'revision', 'request_key', 'request_fingerprint'])]
class DoctorAppointment extends Model
{
    /** @use HasFactory<DoctorAppointmentFactory> */
    use HasFactory;

    public const STATUSES = ['confirmed' => 'مؤكد', 'cancelled' => 'ملغى', 'completed' => 'مكتمل', 'no_show' => 'لم يحضر'];

    protected static function booted(): void
    {
        static::saving(function (self $appointment): void {
            if ($appointment->status !== 'completed' || ($appointment->exists && ! $appointment->isDirty(['status', 'arrived_at']))) {
                return;
            }

            if (! $appointment->arrived_at) {
                throw ValidationException::withMessages(['status' => 'لا يمكن إكمال الموعد قبل تسجيل حضور المريض وإغلاق زيارته الطبية.']);
            }

            if (! $appointment->exists || ! $appointment->visit()->where('doctor_id', $appointment->doctor_id)->where('doctor_facility_id', $appointment->doctor_facility_id)->where('status', 'completed')->whereNotNull('closed_at')->exists()) {
                throw ValidationException::withMessages(['status' => 'إكمال الموعد يتطلب زيارة طبية مغلقة مرتبطة به.']);
            }
        });
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function visit(): HasOne
    {
        return $this->hasOne(DoctorVisit::class);
    }

    public function doctorFacility(): BelongsTo
    {
        return $this->belongsTo(DoctorFacility::class);
    }

    public function doctorService(): BelongsTo
    {
        return $this->belongsTo(DoctorService::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(DoctorAppointmentEvent::class)->orderBy('id');
    }

    public function priceForDisplay(): string
    {
        return self::formatMoney($this->price_minor, $this->currency);
    }

    public static function formatMoney(int $amount, string $currency): string
    {
        $decimals = (int) config('thera.currencies.'.$currency.'.decimals', 2);
        $absolute = abs($amount);
        $scale = 10 ** $decimals;

        return ($amount < 0 ? '-' : '').intdiv($absolute, $scale).($decimals ? '.'.str_pad((string) ($absolute % $scale), $decimals, '0', STR_PAD_LEFT) : '');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DoctorPayment::class);
    }

    public function queueEntry(): HasOne
    {
        return $this->hasOne(DoctorQueueEntry::class);
    }

    public function paidMinor(): int
    {
        return (int) ($this->paid_minor ?? 0);
    }

    public function remainingMinor(): int
    {
        return max(0, $this->price_minor - $this->paidMinor());
    }

    public function paymentStatus(): string
    {
        return $this->remainingMinor() === 0 ? 'paid' : ($this->paidMinor() > 0 ? 'partial' : 'unpaid');
    }

    public function queueState(): string
    {
        if ($this->status !== 'confirmed' || ! $this->starts_at->setTimezone(config('thera.schedule_timezone'))->isToday()) {
            return 'closed';
        }
        if (! $this->arrived_at) {
            return 'not_arrived';
        }
        if ($this->relationLoaded('visit') && $this->visit && ! $this->visit->closed_at) {
            return 'in_visit';
        }
        if ($this->remainingMinor() > 0) {
            return 'waiting_payment';
        }

        return $this->queueEntry ? 'called' : 'ready';
    }

    /** @return array<string, mixed> */
    public function auditSnapshot(): array
    {
        return $this->only(['doctor_facility_id', 'doctor_service_id', 'service_name', 'price_minor', 'currency', 'duration_minutes', 'starts_at', 'ends_at', 'status', 'arrived_at', 'revision']);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['starts_at' => 'immutable_datetime', 'ends_at' => 'immutable_datetime', 'arrived_at' => 'immutable_datetime', 'price_minor' => 'integer', 'duration_minutes' => 'integer', 'revision' => 'integer'];
    }
}
