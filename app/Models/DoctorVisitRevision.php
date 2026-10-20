<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['doctor_id', 'action', 'revision', 'snapshot'])]
class DoctorVisitRevision extends Model
{
    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('إصدارات الزيارة لا تعدل.'));
        static::deleting(fn () => throw new LogicException('إصدارات الزيارة لا تحذف.'));
    }

    public function doctorVisit(): BelongsTo
    {
        return $this->belongsTo(DoctorVisit::class);
    }

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'revision' => 'integer'];
    }
}
