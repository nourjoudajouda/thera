<?php

namespace App\Models;

use Database\Factories\DoctorVisitFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;
use LogicException;

#[Fillable(['doctor_appointment_id', 'doctor_id', 'doctor_facility_id', 'status', 'content', 'revision', 'started_at', 'closed_at'])]
class DoctorVisit extends Model
{
    /** @use HasFactory<DoctorVisitFactory> */
    use HasFactory;

    public const STATUSES = ['draft' => 'مسودة قيد التوثيق', 'initial' => 'تقييم مبدئي', 'pending_results' => 'بانتظار نتيجة الفحوصات', 'completed' => 'زيارة مكتملة'];

    protected static function booted(): void
    {
        static::saving(function (self $visit): void {
            if ($visit->status !== 'completed' && ! $visit->closed_at) {
                return;
            }
            $appointment = $visit->doctorAppointment;
            if ($visit->status !== 'completed' || ! $visit->closed_at || (! trim((string) ($visit->content['assessment'] ?? '')) && ! trim((string) ($visit->content['diagnosis'] ?? ''))) || ! $appointment->arrived_at || $appointment->doctor_id !== $visit->doctor_id || $appointment->doctor_facility_id !== $visit->doctor_facility_id) {
                throw ValidationException::withMessages(['status' => 'إغلاق الزيارة يتطلب حضورًا وسياق موعد صحيحًا وتشخيصًا أو تقييمًا سريريًا واضحًا.']);
            }
        });
        static::updating(function (self $visit): void {
            if ($visit->getOriginal('closed_at')) {
                throw new LogicException('الزيارة المغلقة لا تعدل؛ يلزم إصدار تصحيحي مستقل.');
            }
        });
        static::deleting(function (): void {
            throw new LogicException('لا يجوز حذف الزيارة الطبية.');
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

    public function revisions(): HasMany
    {
        return $this->hasMany(DoctorVisitRevision::class)->orderByDesc('id');
    }

    public function prescription(): HasOne
    {
        return $this->hasOne(DoctorPrescription::class);
    }

    protected function casts(): array
    {
        return ['content' => 'array', 'revision' => 'integer', 'started_at' => 'immutable_datetime', 'closed_at' => 'immutable_datetime'];
    }
}
