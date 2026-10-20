<?php

namespace App\Providers;

use App\Models\DoctorAchievement;
use App\Models\DoctorCredential;
use App\Models\DoctorFacility;
use App\Models\DoctorSpecialty;
use App\Models\DoctorVerificationRequest;
use App\Models\FacilityProposal;
use App\Models\Medication;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(fn (object $doctor, string $token): string => route('doctor.password.reset', [
            'token' => $token,
            'email' => $doctor->getEmailForPasswordReset(),
        ]));

        View::composer('admin.layouts.app', function (ViewInstance $view): void {
            $view->with('adminPendingApprovalCount',
                DoctorVerificationRequest::query()->where('status', DoctorVerificationRequest::STATUS_SUBMITTED)->count()
                + DoctorSpecialty::query()->where('verification_status', DoctorSpecialty::STATUS_PENDING)->count()
                + DoctorCredential::query()->where('verification_status', DoctorCredential::STATUS_PENDING)->count()
                + DoctorAchievement::query()->where('verification_status', DoctorAchievement::STATUS_PENDING)->count()
                + DoctorFacility::query()->where('membership_status', DoctorFacility::STATUS_PENDING)->count()
                + FacilityProposal::query()->where('status', FacilityProposal::STATUS_PENDING)->count()
                + Medication::query()->where('status', 'pending')->count(),
            );
        });
    }
}
