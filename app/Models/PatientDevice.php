<?php

namespace App\Models;

use Database\Factories\PatientDeviceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientDevice extends Model
{
    protected $fillable = ['user_id', 'personal_access_token_id', 'token', 'token_hash', 'platform', 'locale', 'enabled'];

    protected $hidden = ['token', 'token_hash'];

    protected function casts(): array
    {
        return ['token' => 'encrypted', 'enabled' => 'boolean'];
    }

    /** @use HasFactory<PatientDeviceFactory> */
    use HasFactory;
}
