<?php

namespace App\Models;

use Database\Factories\DoctorDiagnosticOrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

#[Fillable(['doctor_id', 'doctor_visit_id', 'kind', 'status', 'priority', 'content', 'issued_snapshot', 'approved_at', 'revision', 'request_key', 'document_version', 'corrects_document_id', 'correction_reason'])]
class DoctorDiagnosticOrder extends Model
{
    /** @use HasFactory<DoctorDiagnosticOrderFactory> */
    use HasFactory;

    protected $attributes = ['document_version' => 1];

    public const KINDS = ['lab' => 'مختبر', 'radiology' => 'أشعة'];

    public const PRIORITIES = ['normal' => 'عادي', 'urgent' => 'عاجل'];

    public const STATUSES = ['draft' => 'مسودة', 'approved' => 'معتمد — لم يوجّه للتنفيذ'];

    protected static function booted(): void
    {
        static::creating(function (self $document): void {
            $previous = $document->correctedFrom;
            if (! $document->corrects_document_id) {
                if ($document->document_version !== 1) {
                    throw new LogicException('المستند الأول يبدأ بالإصدار 1.');
                }
            } elseif (! $previous || ! $previous->approved_at || $previous->status !== 'approved' || $previous->doctor_id !== $document->doctor_id || $previous->doctor_visit_id !== $document->doctor_visit_id || $previous->kind !== $document->kind || $document->document_version !== $previous->document_version + 1 || ! trim((string) $document->correction_reason)) {
                throw new LogicException('التصحيح يجب أن يرتبط بمستند معتمد لنفس الطبيب والزيارة والنوع وسبب محفوظ.');
            }
        });
        static::updating(function (self $order): void {
            if ($order->getOriginal('approved_at')) {
                throw new LogicException('طلب الفحوصات المعتمد لا يعدل بصمت؛ يلزم إصدار تصحيحي مستقل.');
            }
            if ($order->isDirty(['doctor_id', 'doctor_visit_id', 'kind', 'request_key', 'document_version', 'corrects_document_id', 'correction_reason'])) {
                throw new LogicException('هوية الطلب والزيارة ونوعه لا تعدل.');
            }
        });
        static::deleting(fn () => throw new LogicException('لا يجوز حذف طلب الفحوصات الطبية.'));
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
        return $this->hasMany(DoctorDiagnosticOrderRevision::class)->latest('id');
    }

    public function reference(): string
    {
        return ($this->kind === 'lab' ? 'LAB-' : 'RAD-').$this->id;
    }

    public function externalReports(): HasMany
    {
        return $this->hasMany(DoctorExternalReport::class)->latest('id');
    }

    public function correctedFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'corrects_document_id');
    }

    public function correction(): HasOne
    {
        return $this->hasOne(self::class, 'corrects_document_id');
    }

    protected function casts(): array
    {
        return ['content' => 'array', 'issued_snapshot' => 'array', 'approved_at' => 'immutable_datetime', 'revision' => 'integer', 'document_version' => 'integer'];
    }
}
