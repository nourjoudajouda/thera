<?php

namespace App\Models;

use Database\Factories\FacilityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'type', 'city', 'bookings_enabled', 'is_active'])]
class Facility extends Model
{
    public function checkInCode(): string
    {
        return 'THERA:'.$this->id.':'.hash_hmac('sha256', (string) $this->id, config('app.key'));
    }

    /** @use HasFactory<FacilityFactory> */
    use HasFactory;

    public function doctorMemberships(): HasMany
    {
        return $this->hasMany(DoctorFacility::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'bookings_enabled' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
