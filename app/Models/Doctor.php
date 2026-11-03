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
#[Hidden(['password', 'remember_token', 'stamp_path'])]
class Doctor extends Authenticatable
{
    public function stamps(): HasMany
    {
        return $this->hasMany(DoctorStamp::class);
    }

    public const AVAILABILITY = ['unspecified' => 'لم تُحدد الحالة', 'available' => 'متاح الآن', 'busy' => 'مشغول مؤقتًا', 'unavailable' => 'غير متاح مؤقتًا'];

    public const NOTIFICATION_PREFERENCES = ['appointments' => 'الحجوزات وتحديثات المواعيد', 'arrival' => 'وصول المريض', 'results' => 'جاهزية النتائج', 'referrals' => 'الإحالات والمتابعات', 'payments' => 'الدفع والتحصيل'];

    public const PAYMENT_POLICY_TYPES = ['none' => 'بدون دفعة مقدمة', 'percentage' => 'دفعة مقدمة بنسبة', 'full' => 'دفع كامل 100%'];

    public const PAYMENT_REMAINING_TIMINGS = ['before_visit' => 'قبل الزيارة', 'at_arrival' => 'عند الوصول', 'after_visit' => 'بعد الزيارة'];

    /** @return array{preferred_membership_id: ?int, availability: string, notifications: array<string, bool>, require_collection_before_visit: bool, payment_policy: array{type: string, deposit_percent: int, remaining_timing: string, allow_early_payment: bool}} */
    public function accountPreferences(): array
    {
        $stored = $this->account_preferences ?? [];

        return [
            'preferred_membership_id' => $stored['preferred_membership_id'] ?? null,
            'availability' => $stored['availability'] ?? 'unspecified',
            'notifications' => array_replace(array_fill_keys(array_keys(self::NOTIFICATION_PREFERENCES), false), $stored['notifications'] ?? []),
            'require_collection_before_visit' => (bool) ($stored['require_collection_before_visit'] ?? true),
            'payment_policy' => [
                'type' => $stored['payment_policy']['type'] ?? 'percentage',
                'deposit_percent' => (int) ($stored['payment_policy']['deposit_percent'] ?? max(1, min(99, (int) config('thera.doctor_deposit_percentage', 10)))),
                'remaining_timing' => $stored['payment_policy']['remaining_timing'] ?? (($stored['require_collection_before_visit'] ?? true) ? 'before_visit' : 'after_visit'),
                'allow_early_payment' => (bool) ($stored['payment_policy']['allow_early_payment'] ?? true),
            ],
        ];
    }

    public function requiresCollectionBeforeVisit(): bool
    {
        return $this->accountPreferences()['require_collection_before_visit'];
    }

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
            'account_preferences' => 'array',
            'preferences_revision' => 'integer',
        ];
    }
}
