<?php

namespace App\Models;

use Database\Factories\DoctorPrescriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

#[Fillable(['doctor_visit_id', 'doctor_id', 'status', 'content', 'revision', 'approved_at', 'issued_snapshot', 'document_version', 'corrects_prescription_id', 'correction_reason'])]
class DoctorPrescription extends Model
{
    /** @use HasFactory<DoctorPrescriptionFactory> */
    use HasFactory;

    protected $attributes = ['document_version' => 1];

    protected static function booted(): void
    {
        static::creating(function (self $prescription): void {
            if (! $prescription->corrects_prescription_id) {
                if ($prescription->document_version !== 1) {
                    throw new LogicException('الإصدار الأول هو نقطة بدء سلسلة الوصفة.');
                }

                return;
            }
            $previous = $prescription->correctedFrom;
            if (! $previous || ! $previous->approved_at || $previous->status !== 'approved' || $previous->doctor_id !== $prescription->doctor_id || $previous->doctor_visit_id !== $prescription->doctor_visit_id || $prescription->document_version !== $previous->document_version + 1 || ! trim((string) $prescription->correction_reason)) {
                throw new LogicException('التصحيح يجب أن يرتبط بوصفة معتمدة لنفس الطبيب والزيارة وسبب محفوظ.');
            }
        });
        static::updating(function (self $prescription): void {
            if ($prescription->getOriginal('approved_at')) {
                throw new LogicException('الوصفة المعتمدة لا تعدل؛ يلزم إصدار تصحيحي مستقل.');
            }
            if ($prescription->isDirty(['doctor_id', 'doctor_visit_id', 'document_version', 'corrects_prescription_id', 'correction_reason'])) {
                throw new LogicException('هوية إصدار الوصفة وسبب إنشائه لا يعدلان.');
            }
        });
        static::deleting(fn () => throw new LogicException('لا يجوز حذف الوصفة الطبية.'));
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function doctorVisit(): BelongsTo
    {
        return $this->belongsTo(DoctorVisit::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(DoctorPrescriptionRevision::class)->latest('id');
    }

    public function correctedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'corrects_prescription_id');
    }

    public function correction(): HasOne
    {
        return $this->hasOne(self::class, 'corrects_prescription_id');
    }

    protected function casts(): array
    {
        return ['content' => 'array', 'issued_snapshot' => 'array', 'revision' => 'integer', 'document_version' => 'integer', 'approved_at' => 'immutable_datetime'];
    }
}
