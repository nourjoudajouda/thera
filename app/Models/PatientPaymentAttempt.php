<?php

namespace App\Models;

use Database\Factories\PatientPaymentAttemptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientPaymentAttempt extends Model
{
    protected $fillable = ['doctor_appointment_id', 'user_id', 'request_key', 'amount_minor', 'status', 'outcome'];

    protected function casts(): array
    {
        return ['amount_minor' => 'integer'];
    }

    /** @use HasFactory<PatientPaymentAttemptFactory> */
    use HasFactory;
}
