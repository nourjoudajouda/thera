<?php

namespace App\Models;

use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'phone',
    'password',
    'languages',
    'bio',
    'professional_title',
    'years_experience',
    'professional_bio',
    'profile_completed_at',
    'verification_status',
    'terms_accepted_at',
])]
#[Hidden(['password', 'remember_token'])]
class Doctor extends Authenticatable
{
    /** @use HasFactory<DoctorFactory> */
    use HasFactory, Notifiable;

    public function specialtyAssignments(): HasMany
    {
        return $this->hasMany(DoctorSpecialty::class);
    }

    public function primarySpecialtyAssignment(): HasOne
    {
        return $this->hasOne(DoctorSpecialty::class)->where('type', 'primary');
    }

    public function subspecialtyAssignments(): HasMany
    {
        return $this->hasMany(DoctorSpecialty::class)->where('type', 'subspecialty');
    }

    public function facilityMemberships(): HasMany
    {
        return $this->hasMany(DoctorFacility::class);
    }

    public function facilityProposals(): HasMany
    {
        return $this->hasMany(FacilityProposal::class);
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(DoctorCredential::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(DoctorAchievement::class);
    }

    public function verificationRequests(): HasMany
    {
        return $this->hasMany(DoctorVerificationRequest::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'languages' => 'array',
            'years_experience' => 'integer',
            'profile_completed_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
