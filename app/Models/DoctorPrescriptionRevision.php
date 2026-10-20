<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use LogicException;

#[Fillable(['doctor_id', 'action', 'revision', 'snapshot'])]
class DoctorPrescriptionRevision extends Model
{
    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('إصدارات الوصفة لا تعدل.'));
        static::deleting(fn () => throw new LogicException('إصدارات الوصفة لا تحذف.'));
    }

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'revision' => 'integer'];
    }
}
