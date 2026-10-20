<?php

namespace App\Models;

use Database\Factories\DoctorVerificationRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'doctor_id',
    'decided_by',
    'reference',
    'status',
    'documents',
    'profile_snapshot',
    'submitted_at',
    'decided_at',
    'rejection_reason',
])]
class DoctorVerificationRequest extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const DOCUMENT_PROFESSIONAL_LICENSE = 'professional_license';

    public const DOCUMENT_PRIMARY_SPECIALTY_PROOF = 'primary_specialty_proof';

    /** @use HasFactory<DoctorVerificationRequestFactory> */
    use HasFactory;

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'decided_by');
    }

    /** @return array<string, string> */
    public static function requiredDocumentLabels(): array
    {
        return [
            self::DOCUMENT_PROFESSIONAL_LICENSE => 'ترخيص مزاولة المهنة',
            self::DOCUMENT_PRIMARY_SPECIALTY_PROOF => 'إثبات التخصص الرئيسي',
        ];
    }

    public function hasAllRequiredDocuments(): bool
    {
        $documents = $this->documents ?? [];

        return collect(array_keys(self::requiredDocumentLabels()))
            ->every(fn (string $type): bool => filled($documents[$type]['path'] ?? null));
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'documents' => 'array',
            'profile_snapshot' => 'array',
            'submitted_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }
}
