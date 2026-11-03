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

    public const STATUSES = ['draft' => 'مسودة', 'initial' => 'قيد التوثيق', 'pending_results' => 'بانتظار نتائج / مراجعة', 'completed' => 'زيارة مكتملة'];

    /** @param array<string, mixed>|null $content */
    public function workflowStatus(?array $content = null): string
    {
        if ($this->closed_at) {
            return 'completed';
        }
        $orders = $this->diagnosticOrders()->whereNotNull('approved_at')->whereDoesntHave('correction', fn ($query) => $query->whereNotNull('approved_at'));
        $pending = (clone $orders)->where(fn ($query) => $query
            ->whereDoesntHave('externalReports', fn ($query) => $query->whereNotNull('reviewed_at'))
            ->orWhereHas('externalReports', fn ($query) => $query->whereNull('reviewed_at')))->exists();
        if ($pending) {
            return 'pending_results';
        }
        $documented = collect($content ?? $this->content ?? [])->except('diagnosis_type')->flatten()->contains(fn ($value) => trim((string) $value) !== '');

        return $documented || $orders->exists() ? 'initial' : 'draft';
    }

    public function synchronizeWorkflowStatus(): void
    {
        if ($this->closed_at) {
            return;
        }
        $status = $this->workflowStatus();
        if ($status !== $this->status) {
            $previous = $this->status;
            $this->update(['status' => $status]);
            $this->revisions()->create(['doctor_id' => $this->doctor_id, 'action' => 'status_updated', 'revision' => $this->revision, 'snapshot' => ['previous_status' => $previous, 'status' => $status]]);
        }
    }

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
        return $this->hasOne(DoctorPrescription::class)->latestOfMany();
    }

    public function diagnosticOrders(): HasMany
    {
        return $this->hasMany(DoctorDiagnosticOrder::class)->latest('id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(DoctorReferral::class)->latest('id');
    }

    protected function casts(): array
    {
        return ['content' => 'array', 'revision' => 'integer', 'started_at' => 'immutable_datetime', 'closed_at' => 'immutable_datetime'];
    }
}
