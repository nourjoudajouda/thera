<?php

namespace App\Models;

use Database\Factories\PatientReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientReview extends Model
{
    protected $fillable = ['patient_id', 'user_id', 'doctor_id', 'doctor_appointment_id', 'rating', 'comment', 'status', 'reviewed_by', 'review_reason'];

    protected function casts(): array
    {
        return ['rating' => 'integer'];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** @use HasFactory<PatientReviewFactory> */
    use HasFactory;
}
