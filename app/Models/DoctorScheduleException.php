<?php

namespace App\Models;

use Database\Factories\DoctorScheduleExceptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_schedule_id', 'date', 'type', 'periods', 'note'])]
class DoctorScheduleException extends Model
{
    /** @use HasFactory<DoctorScheduleExceptionFactory> */
    use HasFactory;

    public const TYPE_SPECIAL = 'special_hours';

    public const TYPES = ['leave' => 'إجازة', 'closure' => 'إغلاق طارئ', self::TYPE_SPECIAL => 'ساعات خاصة'];

    public function doctorSchedule(): BelongsTo
    {
        return $this->belongsTo(DoctorSchedule::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['date' => 'immutable_date', 'periods' => 'array'];
    }
}
