<?php

namespace App\Models;

use Database\Factories\PatientReminderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientReminder extends Model
{
    protected $fillable = ['patient_id', 'created_by', 'prescription_id', 'title', 'time', 'timezone', 'enabled'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean'];
    }

    /** @use HasFactory<PatientReminderFactory> */
    use HasFactory;
}
