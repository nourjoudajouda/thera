<?php

namespace App\Models;

use Database\Factories\DoctorPaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

#[Fillable(['doctor_appointment_id', 'doctor_id', 'kind', 'amount_minor', 'currency', 'method', 'reference', 'reason', 'reversal_of_id', 'request_key', 'request_fingerprint', 'receipt_snapshot'])]
class DoctorPayment extends Model
{
    /** @use HasFactory<DoctorPaymentFactory> */
    use HasFactory;

    public const METHODS = ['cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي مسجل يدويًا'];

    protected static function booted(): void
    {
        static::updating(function (self $payment): void {
            throw new LogicException('لا يعدّل قيد التحصيل؛ يُسجّل قيد عكسي مستقل مع السبب.');
        });
        static::deleting(function (self $payment): void {
            throw new LogicException('لا يجوز حذف قيد التحصيل.');
        });
    }

    public function doctorAppointment(): BelongsTo
    {
        return $this->belongsTo(DoctorAppointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function reversal(): HasOne
    {
        return $this->hasOne(self::class, 'reversal_of_id');
    }

    public function original(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reversal_of_id');
    }

    public function receiptReference(): string
    {
        return 'THR-'.$this->created_at->format('Y').'-'.str_pad((string) $this->id, 7, '0', STR_PAD_LEFT);
    }

    public function amountForDisplay(): string
    {
        return DoctorAppointment::formatMoney($this->amount_minor, $this->currency);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['amount_minor' => 'integer', 'receipt_snapshot' => 'array', 'created_at' => 'immutable_datetime'];
    }
}
