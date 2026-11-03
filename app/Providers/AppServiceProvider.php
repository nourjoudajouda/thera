<?php

namespace App\Providers;

use App\Models\DoctorAchievement;
use App\Models\DoctorAppointment;
use App\Models\DoctorCredential;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorExternalReport;
use App\Models\DoctorFacility;
use App\Models\DoctorPrescription;
use App\Models\DoctorReferral;
use App\Models\DoctorSpecialty;
use App\Models\DoctorVerificationRequest;
use App\Models\DoctorVisit;
use App\Models\FacilityProposal;
use App\Models\Medication;
use App\Services\PatientNotificationPublisher;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        DoctorAppointment::saved(fn ($a) => app(PatientNotificationPublisher::class)->appointment($a));
        foreach ([DoctorVisit::class => 'visit', DoctorPrescription::class => 'prescription', DoctorDiagnosticOrder::class => 'order', DoctorReferral::class => 'referral', DoctorExternalReport::class => 'report'] as $model => $type) {
            $model::saved(fn ($document) => app(PatientNotificationPublisher::class)->document($document, $type));
        }
        RateLimiter::for('patient-api', fn (Request $request) => Limit::perMinute(60)->by('patient-api|'.($request->user()?->id ?? $request->ip())));
        RateLimiter::for('patient-sensitive', function (Request $request) {
            $identity = $request->input('email', (string) ($request->user()?->id ?? ''));

            return Limit::perMinute(5)->by('patient-sensitive|'.$request->path().'|'.$request->ip().'|'.(is_string($identity) ? mb_strtolower($identity) : 'invalid'));
        });
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
