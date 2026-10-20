<?php

namespace App\Models;

use Database\Factories\SpecialtyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['parent_id', 'name_ar', 'name_en', 'is_active'])]
class Specialty extends Model
{
    /** @use HasFactory<SpecialtyFactory> */
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function doctorAssignments(): HasMany
    {
        return $this->hasMany(DoctorSpecialty::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
