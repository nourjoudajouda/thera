<?php

namespace App\Models;

use Database\Factories\DoctorCredentialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_id', 'type', 'title', 'issuer', 'country', 'issued_on', 'expires_on', 'document_path', 'document_original_name', 'verification_status', 'rejection_reason'])]
class DoctorCredential extends Model
{
    /** @use HasFactory<DoctorCredentialFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'expires_on' => 'date',
        ];
    }
}
