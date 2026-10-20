<?php

namespace App\Models;

use Database\Factories\DoctorSpecialtyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_id', 'specialty_id', 'type', 'verification_status', 'rejection_reason'])]
class DoctorSpecialty extends Model
{
    /** @use HasFactory<DoctorSpecialtyFactory> */
    use HasFactory;

    public const TYPE_PRIMARY = 'primary';

    public const TYPE_SUBSPECIALTY = 'subspecialty';

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }
}
