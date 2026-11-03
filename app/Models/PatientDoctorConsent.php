<?php

namespace App\Models;

use Database\Factories\PatientDoctorConsentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDoctorConsent extends Model
{
    protected $fillable = ['patient_id', 'doctor_id', 'granted_by', 'expires_at', 'revoked_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'immutable_datetime', 'revoked_at' => 'immutable_datetime'];
    }

    /** @use HasFactory<PatientDoctorConsentFactory> */
    use HasFactory;
}
