<?php

namespace App\Models;

use Database\Factories\DoctorQueueEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_appointment_id', 'doctor_id', 'called_at'])]
class DoctorQueueEntry extends Model
{
    /** @use HasFactory<DoctorQueueEntryFactory> */
    use HasFactory;

    public function doctorAppointment(): BelongsTo
    {
        return $this->belongsTo(DoctorAppointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['called_at' => 'immutable_datetime'];
    }
}
