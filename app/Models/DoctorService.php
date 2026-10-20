<?php

namespace App\Models;

use Database\Factories\DoctorServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_facility_id', 'name', 'description', 'price_minor', 'currency', 'duration_minutes', 'instructions', 'is_active'])]
class DoctorService extends Model
{
    /** @use HasFactory<DoctorServiceFactory> */
    use HasFactory;

    public function doctorFacility(): BelongsTo
    {
        return $this->belongsTo(DoctorFacility::class);
    }

    public function isBookingEnabled(): bool
    {
        $membership = $this->doctorFacility;

        return $this->is_active && $membership->membership_status === DoctorFacility::STATUS_ACTIVE
            && $membership->doctor->verification_status === 'verified'
            && $membership->facility->is_active && $membership->facility->bookings_enabled;
    }

    #[Scope]
    protected function bookingEnabled(Builder $query): void
    {
        $query->where('is_active', true)->whereHas('doctorFacility', fn (Builder $membership) => $membership
            ->where('membership_status', DoctorFacility::STATUS_ACTIVE)
            ->whereHas('doctor', fn (Builder $doctor) => $doctor->where('verification_status', 'verified'))
            ->whereHas('facility', fn (Builder $facility) => $facility->where('is_active', true)->where('bookings_enabled', true)));
    }

    public static function priceToMinor(string $price, string $currency): int
    {
        $decimals = (int) config("thera.currencies.{$currency}.decimals");
        [$whole, $fraction] = array_pad(explode('.', $price, 2), 2, '');

        return (int) $whole * (10 ** $decimals) + (int) str_pad($fraction, $decimals, '0');
    }

    public function priceForInput(): string
    {
        $decimals = (int) config("thera.currencies.{$this->currency}.decimals");
        $factor = 10 ** $decimals;

        return intdiv($this->price_minor, $factor).'.'.str_pad((string) ($this->price_minor % $factor), $decimals, '0', STR_PAD_LEFT);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['price_minor' => 'integer', 'duration_minutes' => 'integer', 'is_active' => 'boolean'];
    }
}
