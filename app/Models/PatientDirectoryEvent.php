<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['doctor_id', 'patient_id', 'doctor_appointment_id', 'action'])]
class PatientDirectoryEvent extends Model {}
