<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Account\UpdateProfessionalProfileRequest;
use App\Models\Doctor;
use App\Models\DoctorAchievement;
use App\Models\DoctorCredential;
use App\Models\DoctorFacility;
use App\Models\DoctorService;
use App\Models\DoctorSpecialty;
use App\Models\Facility;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfessionalProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $doctor = $request->user('doctor');

        abort_unless($doctor instanceof Doctor, 403);

        $doctor->load([
            'specialtyAssignments.specialty',
            'facilityMemberships.facility',
            'facilityProposals',
            'credentials',
            'achievements',
        ]);

        $primarySpecialtyAssignment = $doctor->specialtyAssignments
            ->firstWhere('type', DoctorSpecialty::TYPE_PRIMARY);
        $subspecialtyAssignments = $doctor->specialtyAssignments
            ->where('type', DoctorSpecialty::TYPE_SUBSPECIALTY)
            ->values();
        $publicFacilityMemberships = $doctor->facilityMemberships
            ->filter(fn (DoctorFacility $membership): bool => $membership->membership_status === DoctorFacility::STATUS_ACTIVE
                && $membership->is_public
                && $membership->facility->is_active)
            ->values();
        $verifiedCredentials = $doctor->credentials
            ->where('verification_status', DoctorCredential::STATUS_VERIFIED)
            ->sortByDesc('issued_on')
            ->values();
        $verifiedAchievements = $doctor->achievements
            ->where('verification_status', DoctorAchievement::STATUS_VERIFIED)
            ->sortByDesc('year')
            ->values();
        $verifiedSubspecialtyAssignments = $subspecialtyAssignments
            ->where('verification_status', DoctorSpecialty::STATUS_VERIFIED)
            ->values();
        $availableFacilities = Facility::query()
            ->where('is_active', true)
            ->whereNotIn('id', $doctor->facilityMemberships->pluck('facility_id'))
            ->orderBy('name')
            ->get();

        $completedFields = collect([
            $doctor->name,
            $doctor->professional_title,
            $doctor->years_experience,
            $doctor->professional_bio,
            $primarySpecialtyAssignment,
        ])->filter(fn (mixed $value): bool => filled($value))->count();

        $primarySpecialties = Specialty::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name_ar')
            ->get();

        $availableSubspecialties = Specialty::query()
            ->when(
                $primarySpecialtyAssignment,
                fn ($query) => $query->where('parent_id', $primarySpecialtyAssignment->specialty_id),
                fn ($query) => $query->whereRaw('1 = 0'),
            )
            ->where('is_active', true)
            ->whereNotIn('id', $subspecialtyAssignments->pluck('specialty_id'))
            ->orderBy('name_ar')
            ->get();

        return view('doctor.account.professional-profile', [
            'doctor' => $doctor,
            'activeServiceCount' => DoctorService::query()->whereHas('doctorFacility', fn ($query) => $query->whereBelongsTo($doctor))->bookingEnabled()->count(),
            'profileCompletion' => (int) round(($completedFields / 5) * 100),
            'primarySpecialtyAssignment' => $primarySpecialtyAssignment,
            'subspecialtyAssignments' => $subspecialtyAssignments,
            'primarySpecialties' => $primarySpecialties,
            'availableSubspecialties' => $availableSubspecialties,
            'facilityMemberships' => $doctor->facilityMemberships->sortByDesc('created_at')->values(),
            'facilityProposals' => $doctor->facilityProposals->sortByDesc('created_at')->values(),
            'availableFacilities' => $availableFacilities,
            'credentials' => $doctor->credentials->sortByDesc('created_at')->values(),
            'achievements' => $doctor->achievements->sortByDesc('created_at')->values(),
            'publicFacilityMemberships' => $publicFacilityMemberships,
            'verifiedCredentials' => $verifiedCredentials,
            'verifiedAchievements' => $verifiedAchievements,
            'verifiedSubspecialtyAssignments' => $verifiedSubspecialtyAssignments,
        ]);
    }

    public function update(UpdateProfessionalProfileRequest $request): RedirectResponse
    {
        $doctor = $request->user('doctor');

        abort_unless($doctor instanceof Doctor, 403);

        DB::transaction(function () use ($doctor, $request): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($doctor->id);
            if ($doctor->verificationRequests()->where('status', 'submitted')->exists()) {
                throw ValidationException::withMessages(['verification' => 'لا يمكن تعديل البيانات المهنية أثناء مراجعة طلب التحقق.']);
            }
            $doctor->update([
                ...$request->safe()->only([
                    'name',
                    'languages',
                    'bio',
                    'professional_title',
                    'years_experience',
                    'professional_bio',
                ]),
                'profile_completed_at' => $doctor->profile_completed_at ?? now(),
            ]);
        });

        if ($request->boolean('next')) {
            return redirect()
                ->route('doctor.account.verification', ['onboarding' => 1])
                ->with('status', 'تم حفظ الملف المهني. أكمل الآن بيانات التحقق المهني.');
        }

        $routeParameters = $request->boolean('onboarding') ? ['onboarding' => 1] : [];

        return redirect()
            ->route('doctor.account.professional-profile', $routeParameters)
            ->with('status', 'تم حفظ الملف المهني بنجاح.');
    }
}
