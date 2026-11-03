<?php

namespace App\Models;

use Database\Factories\PatientDelegationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientDelegation extends Model
{
    protected $fillable = ['patient_id', 'user_id', 'kind', 'relationship', 'status', 'proof_path', 'reviewed_by', 'review_reason', 'expires_at'];

    protected $hidden = ['proof_path'];

    protected function casts(): array
    {
        return ['expires_at' => 'immutable_datetime'];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @use HasFactory<PatientDelegationFactory> */
    use HasFactory;
}
