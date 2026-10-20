<?php

namespace App\Models;

use Database\Factories\DoctorFacilityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['doctor_id', 'facility_id', 'role', 'membership_status', 'rejection_reason', 'is_public'])]
class DoctorFacility extends Model
{
    /** @use HasFactory<DoctorFacilityFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PENDING = 'pending';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SUSPENDED = 'suspended';

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(DoctorService::class);
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(DoctorSchedule::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }
}
