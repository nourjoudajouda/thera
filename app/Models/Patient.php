<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'name', 'phone', 'phone_verified_at', 'is_active', 'booking_enabled'])]
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
        return ['phone_verified_at' => 'immutable_datetime', 'is_active' => 'boolean', 'booking_enabled' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
