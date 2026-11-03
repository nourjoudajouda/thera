<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'name', 'phone', 'phone_verified_at', 'is_active', 'booking_enabled', 'birth_date', 'gender', 'address', 'health_information'])]
class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $patient): void {
            $patient->public_id ??= (string) Str::ulid();
        });
        static::updating(function (self $patient): void {
            if ($patient->isDirty('public_id')) {
                throw new \LogicException('رقم ملف ثيرا ثابت ولا يُستبدل.');
            }
        });
    }

    protected function casts(): array
    {
        return ['birth_date' => 'immutable_date', 'health_information' => 'array', 'phone_verified_at' => 'immutable_datetime', 'is_active' => 'boolean', 'booking_enabled' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function delegations(): HasMany
    {
        return $this->hasMany(PatientDelegation::class);
    }

    #[Scope]
    protected function accessibleTo(Builder $query, User $user): void
    {
        if (! self::query()->where('user_id', $user->id)->where('is_active', true)->exists()) {
            $query->whereRaw('1 = 0');

            return;
        }
        $query->where('is_active', true)->where(fn ($q) => $q->where('user_id', $user->id)->orWhereHas('delegations', fn ($q) => $q->where('user_id', $user->id)->where('status', 'approved')->where('expires_at', '>', now())));
    }

    public function recordAccess(string $action, ?int $userId = null, ?int $doctorId = null, ?int $adminId = null, array $detail = []): void
    {
        DB::table('patient_access_events')->insert(['patient_id' => $this->id, 'user_id' => $userId, 'doctor_id' => $doctorId, 'admin_id' => $adminId, 'action' => $action, 'detail' => json_encode($detail, JSON_THROW_ON_ERROR), 'created_at' => now()]);
    }

    public function hasDoctorConsent(Doctor $doctor): bool
    {
        $consent = PatientDoctorConsent::where('patient_id', $this->id)->where('doctor_id', $doctor->id)->whereNull('revoked_at')->where('expires_at', '>', now())->first();
        $grantor = $consent ? User::find($consent->granted_by) : null;

        return $grantor && self::query()->accessibleTo($grantor)->whereKey($this->id)->exists();
    }

    public function reference(): string
    {
        return 'TH-'.$this->public_id;
    }

    public function maskedPhone(): string
    {
        return str_repeat('•', max(0, strlen($this->phone) - 4)).substr($this->phone, -4);
    }
}
