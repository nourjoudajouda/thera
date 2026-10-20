<?php

namespace App\Models;

use Database\Factories\DoctorAchievementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_id', 'type', 'title', 'organization', 'year', 'description', 'evidence_path', 'evidence_original_name', 'verification_status', 'rejection_reason'])]
class DoctorAchievement extends Model
{
    /** @use HasFactory<DoctorAchievementFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
