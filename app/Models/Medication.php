<?php

namespace App\Models;

use Database\Factories\MedicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['doctor_id', 'name', 'active_ingredient', 'strength', 'dosage_form', 'fingerprint', 'status', 'decided_by', 'decided_at', 'rejection_reason'])]
class Medication extends Model
{
    /** @use HasFactory<MedicationFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::updating(function (self $medication): void {
            if ($medication->getOriginal('status') !== 'pending' || $medication->isDirty(['name', 'active_ingredient', 'strength', 'dosage_form', 'fingerprint', 'doctor_id'])) {
                throw new LogicException('تعريف الدواء وقرار مراجعته لا يعدلان بصمت.');
            }
        });
        static::deleting(fn () => throw new LogicException('لا يجوز حذف دواء مرتبط بالدليل.'));
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /** @param array{name:string,active_ingredient:string,strength:string,dosage_form:string} $data */
    public static function fingerprintFor(array $data): string
    {
        return hash('sha256', json_encode(array_map(fn (string $key): string => mb_strtolower(trim($data[$key])), ['name', 'active_ingredient', 'strength', 'dosage_form']), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    protected function casts(): array
    {
        return ['decided_at' => 'immutable_datetime'];
    }
}
