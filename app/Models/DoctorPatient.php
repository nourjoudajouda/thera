<?php

namespace App\Models;

use Database\Factories\DoctorPatientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

#[Fillable(['doctor_id', 'patient_id', 'name', 'phone'])]
class DoctorPatient extends Model
{
    /** @use HasFactory<DoctorPatientFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('هوية الملف المحلي لا تستبدل بصمت.'));
        static::deleting(fn () => throw new LogicException('لا يجوز حذف ملف مريض مرتبط بالسجل الطبي.'));
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function theraPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(DoctorAppointment::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(DoctorPatientEvent::class)->latest('id');
    }

    public function reference(): string
    {
        return 'PT-'.$this->id;
    }
}
