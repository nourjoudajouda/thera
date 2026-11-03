<?php

namespace App\Models;

use Database\Factories\PatientFavoriteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientFavorite extends Model
{
    protected $fillable = ['user_id', 'doctor_id'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** @use HasFactory<PatientFavoriteFactory> */
    use HasFactory;
}
