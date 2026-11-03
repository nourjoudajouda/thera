<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use LogicException;

#[Fillable(['doctor_id', 'action', 'revision', 'snapshot'])]
class DoctorReferralRevision extends Model
{
    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('سجل المستند لا يعدل.'));
        static::deleting(fn () => throw new LogicException('سجل المستند لا يحذف.'));
    }

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'revision' => 'integer'];
    }
}
