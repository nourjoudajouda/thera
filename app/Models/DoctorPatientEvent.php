<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use LogicException;

#[Fillable(['doctor_id', 'doctor_appointment_id', 'action', 'request_key', 'snapshot'])]
class DoctorPatientEvent extends Model
{
    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('سجل ملف المريض لا يعدل.'));
        static::deleting(fn () => throw new LogicException('سجل ملف المريض لا يحذف.'));
    }

    protected function casts(): array
    {
        return ['snapshot' => 'array'];
    }
}
