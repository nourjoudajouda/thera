<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_id', 'action', 'reason', 'before', 'after'])]
class DoctorAppointmentEvent extends Model
{
    public const ACTIONS = ['created' => 'إنشاء الموعد', 'rescheduled' => 'إعادة جدولة', 'cancelled' => 'إلغاء الموعد', 'arrived' => 'تسجيل حضور يدوي', 'no_show' => 'تسجيل عدم الحضور', 'payment_recorded' => 'تسجيل تحصيل يدوي', 'payment_reversed' => 'عكس قيد تحصيل', 'patient_called' => 'استدعاء المريض', 'visit_started' => 'بدء الزيارة الطبية', 'visit_completed' => 'إغلاق الزيارة وإكمال الموعد'];

    public function doctorAppointment(): BelongsTo
    {
        return $this->belongsTo(DoctorAppointment::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['before' => 'array', 'after' => 'array'];
    }
}
