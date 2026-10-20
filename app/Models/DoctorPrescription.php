<?php

namespace App\Models;

use Database\Factories\DoctorPrescriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable(['doctor_visit_id', 'doctor_id', 'status', 'content', 'revision', 'approved_at', 'issued_snapshot'])]
class DoctorPrescription extends Model
{
    /** @use HasFactory<DoctorPrescriptionFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::updating(function (self $prescription): void {
            if ($prescription->getOriginal('approved_at')) {
                throw new LogicException('الوصفة المعتمدة لا تعدل؛ يلزم إصدار تصحيحي مستقل.');
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

    protected function casts(): array
    {
        return ['content' => 'array', 'issued_snapshot' => 'array', 'revision' => 'integer', 'approved_at' => 'immutable_datetime'];
    }
}
