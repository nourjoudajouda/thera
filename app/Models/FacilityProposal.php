<?php

namespace App\Models;

use Database\Factories\FacilityProposalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['doctor_id', 'name', 'type', 'city', 'role', 'status', 'rejection_reason'])]
class FacilityProposal extends Model
{
    /** @use HasFactory<FacilityProposalFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
