<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAchievement;
use App\Models\DoctorCredential;
use App\Models\DoctorFacility;
use App\Models\DoctorSpecialty;
use App\Models\DoctorVerificationRequest;
use App\Models\FacilityProposal;
use App\Models\Medication;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $pendingCounts = [
            'doctor_verifications' => DoctorVerificationRequest::query()->where('status', DoctorVerificationRequest::STATUS_SUBMITTED)->count(),
            'specialties' => DoctorSpecialty::query()->where('verification_status', DoctorSpecialty::STATUS_PENDING)->count(),
            'credentials' => DoctorCredential::query()->where('verification_status', DoctorCredential::STATUS_PENDING)->count(),
            'achievements' => DoctorAchievement::query()->where('verification_status', DoctorAchievement::STATUS_PENDING)->count(),
            'facility_memberships' => DoctorFacility::query()->where('membership_status', DoctorFacility::STATUS_PENDING)->count(),
            'facility_proposals' => FacilityProposal::query()->where('status', FacilityProposal::STATUS_PENDING)->count(),
        ];

        return view('admin.dashboard', [
            'doctorCount' => Doctor::query()->count(),
            'completedProfileCount' => Doctor::query()->whereNotNull('profile_completed_at')->count(),
            'verifiedDoctorCount' => Doctor::query()->where('verification_status', 'verified')->count(),
            'pendingCounts' => $pendingCounts,
            'pendingTotal' => array_sum($pendingCounts) + Medication::query()->where('status', 'pending')->count(),
            'medicationPendingCount' => Medication::query()->where('status', 'pending')->count(),
            'recentDoctors' => Doctor::query()->latest()->limit(6)->get(),
        ]);
    }
}
