<?php

namespace App\Models;

use Database\Factories\PatientNotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientNotification extends Model
{
    protected $fillable = ['patient_id', 'user_id', 'event_key', 'kind', 'target_type', 'target_id', 'read_at', 'push_state'];

    protected function casts(): array
    {
        return ['read_at' => 'immutable_datetime'];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /** @use HasFactory<PatientNotificationFactory> */
    use HasFactory;
}
