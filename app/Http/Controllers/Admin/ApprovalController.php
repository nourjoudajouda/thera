<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateApprovalRequest;
use App\Models\DoctorAchievement;
use App\Models\DoctorCredential;
use App\Models\DoctorFacility;
use App\Models\DoctorSpecialty;
use App\Models\DoctorVerificationRequest;
use App\Models\Facility;
use App\Models\FacilityProposal;
use App\Models\Medication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /** @var array<string, class-string<Model>> */
    private const APPROVABLE_TYPES = [
        'specialty' => DoctorSpecialty::class,
        'credential' => DoctorCredential::class,
        'achievement' => DoctorAchievement::class,
        'facility-membership' => DoctorFacility::class,
        'facility-proposal' => FacilityProposal::class,
        'doctor-verification' => DoctorVerificationRequest::class,
    ];

    public function index(): View
    {
        return view('admin.approvals.index', [
            'medications' => Medication::query()->with('doctor:id,name')->where('status', 'pending')->latest()->limit(50)->get(),
            'specialties' => DoctorSpecialty::query()
                ->with(['doctor:id,name,email', 'specialty:id,name_ar'])
                ->where('verification_status', DoctorSpecialty::STATUS_PENDING)
                ->latest()
                ->limit(50)
                ->get(),
            'credentials' => DoctorCredential::query()
                ->with('doctor:id,name,email')
                ->where('verification_status', DoctorCredential::STATUS_PENDING)
                ->latest()
                ->limit(50)
                ->get(),
            'achievements' => DoctorAchievement::query()
                ->with('doctor:id,name,email')
                ->where('verification_status', DoctorAchievement::STATUS_PENDING)
                ->latest()
                ->limit(50)
                ->get(),
            'facilityMemberships' => DoctorFacility::query()
                ->with(['doctor:id,name,email', 'facility:id,name,city'])
                ->where('membership_status', DoctorFacility::STATUS_PENDING)
                ->latest()
                ->limit(50)
                ->get(),
            'facilityProposals' => FacilityProposal::query()
                ->with('doctor:id,name,email')
                ->where('status', FacilityProposal::STATUS_PENDING)
                ->latest()
                ->limit(50)
                ->get(),
            'verificationRequests' => DoctorVerificationRequest::query()
                ->with('doctor:id,name,email,professional_title')
                ->where('status', DoctorVerificationRequest::STATUS_SUBMITTED)
                ->latest('submitted_at')
                ->limit(50)
                ->get(),
        ]);
    }

    public function update(UpdateApprovalRequest $request, string $type, int $id): RedirectResponse
    {
        $modelClass = self::APPROVABLE_TYPES[$type] ?? null;
        abort_unless($modelClass, 404);

        $validated = $request->validated();
        $isApproved = $validated['decision'] === 'approve';
        $adminId = $request->user('admin')->getAuthIdentifier();

        DB::transaction(function () use ($modelClass, $type, $id, $isApproved, $validated, $adminId): void {
            $approval = $modelClass::query()->lockForUpdate()->findOrFail($id);
            abort_unless($this->isPending($approval), 409, 'تمت معالجة هذا الطلب مسبقًا.');

            if ($approval instanceof DoctorVerificationRequest) {
                $doctor = $approval->doctor()->lockForUpdate()->firstOrFail();
                $approval->update([
                    'status' => $isApproved
                        ? DoctorVerificationRequest::STATUS_APPROVED
                        : DoctorVerificationRequest::STATUS_REJECTED,
                    'decided_by' => $adminId,
                    'decided_at' => now(),
                    'rejection_reason' => $isApproved ? null : $validated['rejection_reason'],
                ]);
                $doctor->update([
                    'verification_status' => $isApproved ? 'verified' : 'rejected',
                ]);

                return;
            }

            if ($approval instanceof FacilityProposal && $isApproved) {
                $this->approveFacilityProposal($approval);

                return;
            }

            $statusAttribute = $this->statusAttribute($type);
            $approval->forceFill([
                $statusAttribute => $this->decisionStatus($type, $isApproved),
                'rejection_reason' => $isApproved ? null : $validated['rejection_reason'],
                ...($approval instanceof DoctorFacility ? ['is_public' => false] : []),
            ])->save();
        });

        return redirect()
            ->route('admin.approvals.index')
            ->with('status', $isApproved ? 'تم اعتماد الطلب بنجاح.' : 'تم رفض الطلب وحفظ السبب للطبيب.');
    }

    private function isPending(Model $approval): bool
    {
        return match (true) {
            $approval instanceof DoctorVerificationRequest => $approval->status === DoctorVerificationRequest::STATUS_SUBMITTED,
            $approval instanceof DoctorFacility => $approval->membership_status === DoctorFacility::STATUS_PENDING,
            $approval instanceof FacilityProposal => $approval->status === FacilityProposal::STATUS_PENDING,
            default => $approval->getAttribute('verification_status') === 'pending',
        };
    }

    private function statusAttribute(string $type): string
    {
        return match ($type) {
            'facility-membership' => 'membership_status',
            'facility-proposal' => 'status',
            default => 'verification_status',
        };
    }

    private function decisionStatus(string $type, bool $isApproved): string
    {
        if ($isApproved) {
            return match ($type) {
                'facility-membership' => DoctorFacility::STATUS_ACTIVE,
                'facility-proposal' => FacilityProposal::STATUS_APPROVED,
                default => 'verified',
            };
        }

        return match ($type) {
            'facility-membership' => DoctorFacility::STATUS_REJECTED,
            'facility-proposal' => FacilityProposal::STATUS_REJECTED,
            default => 'rejected',
        };
    }

    private function approveFacilityProposal(FacilityProposal $proposal): void
    {
        $facility = Facility::query()->firstOrCreate(
            ['name' => $proposal->name, 'city' => $proposal->city],
            [
                'type' => $proposal->type,
                'bookings_enabled' => false,
                'is_active' => true,
            ],
        );

        DoctorFacility::query()->updateOrCreate(
            [
                'doctor_id' => $proposal->doctor_id,
                'facility_id' => $facility->id,
            ],
            [
                'role' => $proposal->role,
                'membership_status' => DoctorFacility::STATUS_ACTIVE,
                'is_public' => false,
            ],
        );

        $proposal->forceFill([
            'status' => FacilityProposal::STATUS_APPROVED,
            'rejection_reason' => null,
        ])->save();
    }
}
