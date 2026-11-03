<?php

use App\Http\Controllers\Doctor\Account\DoctorAchievementController;
use App\Http\Controllers\Doctor\Account\DoctorCredentialController;
use App\Http\Controllers\Doctor\Account\DoctorFacilityController;
use App\Http\Controllers\Doctor\Account\DoctorFacilityProposalController;
use App\Http\Controllers\Doctor\Account\DoctorSettingsController;
use App\Http\Controllers\Doctor\Account\DoctorSpecialtyController;
use App\Http\Controllers\Doctor\Account\DoctorStampController;
use App\Http\Controllers\Doctor\Account\ProfessionalProfileController;
use App\Http\Controllers\Doctor\Account\VerificationController;
use App\Http\Controllers\Doctor\Appointments\DoctorAppointmentController;
use App\Http\Controllers\Doctor\Appointments\DoctorQueueController;
use App\Http\Controllers\Doctor\Appointments\FacilityCheckInController;
use App\Http\Controllers\Doctor\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Doctor\Auth\NewPasswordController;
use App\Http\Controllers\Doctor\Auth\PasswordResetLinkController;
use App\Http\Controllers\Doctor\Auth\RegisteredDoctorController;
use App\Http\Controllers\Doctor\Clinical\DoctorDocumentCorrectionController;
use App\Http\Controllers\Doctor\Clinical\DoctorDocumentPrintController;
use App\Http\Controllers\Doctor\Clinical\DoctorPrescriptionController;
use App\Http\Controllers\Doctor\Clinical\DoctorPrescriptionPrintController;
use App\Http\Controllers\Doctor\Clinical\DoctorReferralController;
use App\Http\Controllers\Doctor\Clinical\DoctorVisitController;
use App\Http\Controllers\Doctor\DoctorDashboardController;
use App\Http\Controllers\Doctor\DoctorPatientController;
use App\Http\Controllers\Doctor\DoctorShellController;
use App\Http\Controllers\Doctor\Finance\DoctorCollectionController;
use App\Http\Controllers\Doctor\Orders\DoctorDiagnosticOrderController;
use App\Http\Controllers\Doctor\Orders\DoctorExternalReportController;
use App\Http\Controllers\Doctor\PatientSharedRecordController;
use App\Http\Controllers\Doctor\Practice\DoctorScheduleController;
use App\Http\Controllers\Doctor\Practice\DoctorServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('doctor')->name('doctor.')->group(function (): void {
    Route::middleware('guest:doctor')->group(function (): void {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
        Route::get('register', [RegisteredDoctorController::class, 'create'])->name('register');
        Route::post('register', [RegisteredDoctorController::class, 'store'])->name('register.store');
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('password.email');
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
    });

    Route::middleware('auth:doctor')->group(function (): void {
        Route::get('patients/{doctorPatient}/shared-record', [PatientSharedRecordController::class, 'show'])->name('patients.shared-record');
        Route::get('patients/{doctorPatient}/shared-record/{type}/{id}/file', [PatientSharedRecordController::class, 'download'])->whereIn('type', ['visit', 'prescription', 'order', 'referral', 'report'])->name('patients.shared-document');
        Route::get('facilities/{facility}/check-in', [FacilityCheckInController::class, 'show'])->name('facilities.check-in');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('shell/notifications', [DoctorShellController::class, 'notifications'])->middleware('throttle:60,1')->name('shell.notifications');
        Route::get('account/stamp', [DoctorStampController::class, 'show'])->name('account.stamp.show');
        Route::post('account/stamp', [DoctorStampController::class, 'store'])->middleware('throttle:10,1')->name('account.stamp.store');
        Route::get('/', DoctorDashboardController::class)->name('dashboard');

        Route::prefix('account')->name('account.')->group(function (): void {
            Route::get('verification', [VerificationController::class, 'show'])->name('verification');
            Route::post('verification/documents', [VerificationController::class, 'storeDocument'])
                ->middleware('throttle:10,1')
                ->name('verification.documents.store');
            Route::get('verification/requests/{verificationRequest}/documents/{documentType}', [VerificationController::class, 'download'])
                ->name('verification.documents.download');
            Route::post('verification/submit', [VerificationController::class, 'submit'])
                ->middleware('throttle:5,1')
                ->name('verification.submit');
            Route::post('verification/restart', [VerificationController::class, 'restart'])
                ->middleware('throttle:5,1')->name('verification.restart');
            Route::get('professional-profile', [ProfessionalProfileController::class, 'edit'])
                ->name('professional-profile');
            Route::put('professional-profile', [ProfessionalProfileController::class, 'update'])
                ->name('professional-profile.update');
            Route::post('professional-profile/specialties', [DoctorSpecialtyController::class, 'store'])
                ->name('professional-profile.specialties.store');
            Route::delete('professional-profile/specialties/{doctorSpecialty}', [DoctorSpecialtyController::class, 'destroy'])
                ->name('professional-profile.specialties.destroy');
            Route::patch('professional-profile/facilities/{doctorFacility}', [DoctorFacilityController::class, 'update'])
                ->name('professional-profile.facilities.update');
            Route::post('professional-profile/facilities', [DoctorFacilityController::class, 'store'])
                ->name('professional-profile.facilities.store');
            Route::delete('professional-profile/facilities/{doctorFacility}', [DoctorFacilityController::class, 'destroy'])
                ->name('professional-profile.facilities.destroy');
            Route::post('professional-profile/facility-proposals', [DoctorFacilityProposalController::class, 'store'])
                ->name('professional-profile.facility-proposals.store');
            Route::delete('professional-profile/facility-proposals/{facilityProposal}', [DoctorFacilityProposalController::class, 'destroy'])
                ->name('professional-profile.facility-proposals.destroy');
            Route::post('professional-profile/credentials', [DoctorCredentialController::class, 'store'])
                ->name('professional-profile.credentials.store');
            Route::get('professional-profile/credentials/{doctorCredential}/document', [DoctorCredentialController::class, 'download'])
                ->name('professional-profile.credentials.document');
            Route::delete('professional-profile/credentials/{doctorCredential}', [DoctorCredentialController::class, 'destroy'])
                ->name('professional-profile.credentials.destroy');
            Route::post('professional-profile/achievements', [DoctorAchievementController::class, 'store'])
                ->name('professional-profile.achievements.store');
            Route::get('professional-profile/achievements/{doctorAchievement}/evidence', [DoctorAchievementController::class, 'download'])
                ->name('professional-profile.achievements.evidence');
            Route::delete('professional-profile/achievements/{doctorAchievement}', [DoctorAchievementController::class, 'destroy'])
                ->name('professional-profile.achievements.destroy');
            Route::get('settings', [DoctorSettingsController::class, 'edit'])->name('settings');
            Route::put('settings', [DoctorSettingsController::class, 'update'])->middleware('throttle:20,1')->name('settings.update');
        });

        Route::prefix('appointments')->name('appointments.')->group(function (): void {
            Route::get('/', [DoctorAppointmentController::class, 'index'])->name('index');
            Route::get('create', [DoctorAppointmentController::class, 'create'])->name('create');
            Route::get('details', [DoctorAppointmentController::class, 'show'])->name('show');
            Route::get('availability', [DoctorAppointmentController::class, 'availability'])->name('availability');
            Route::get('contacts', [DoctorAppointmentController::class, 'contacts'])->name('contacts');
            Route::get('thera-patients', [DoctorAppointmentController::class, 'theraPatients'])->middleware('throttle:20,1')->name('thera-patients');
            Route::post('/', [DoctorAppointmentController::class, 'store'])->name('store');
            Route::get('{doctorAppointment}/edit', [DoctorAppointmentController::class, 'edit'])->name('edit');
            Route::put('{doctorAppointment}', [DoctorAppointmentController::class, 'update'])->name('update');
            Route::patch('{doctorAppointment}/status', [DoctorAppointmentController::class, 'status'])->name('status');
            Route::post('{doctorAppointment}/queue', [DoctorQueueController::class, 'store'])->name('queue.store');
        });

        Route::prefix('patients')->name('patients.')->group(function (): void {
            Route::get('/', [DoctorPatientController::class, 'index'])->name('index');
            Route::get('details', [DoctorPatientController::class, 'show'])->name('show');
        });

        Route::prefix('clinical')->name('clinical.')->group(function (): void {
            Route::get('visits', [DoctorVisitController::class, 'index'])->name('visits');
            Route::get('visit', [DoctorVisitController::class, 'show'])->name('visit');
            Route::post('visits/{doctorAppointment}/start', [DoctorVisitController::class, 'store'])->name('visits.start');
            Route::put('visits/{doctorVisit}', [DoctorVisitController::class, 'update'])->name('visits.update');
            Route::get('prescriptions', [DoctorPrescriptionController::class, 'index'])->name('prescriptions');
            Route::get('prescriptions/{doctorPrescription}', [DoctorPrescriptionController::class, 'show'])->name('prescriptions.show');
            Route::get('prescriptions/{doctorPrescription}/print', [DoctorPrescriptionPrintController::class, 'show'])->name('prescriptions.print');
            Route::post('prescriptions/{doctorPrescription}/print', [DoctorPrescriptionPrintController::class, 'store'])->middleware('throttle:20,1')->name('prescriptions.print.store');
            Route::put('prescriptions/{doctorPrescription}', [DoctorPrescriptionController::class, 'update'])->name('prescriptions.update');
            Route::post('prescriptions/{doctorPrescription}/correction', [DoctorPrescriptionController::class, 'correct'])->middleware('throttle:10,1')->name('prescriptions.correct');
            Route::post('visits/{doctorVisit}/prescription', [DoctorPrescriptionController::class, 'store'])->name('prescriptions.store');
            Route::get('referrals', [DoctorReferralController::class, 'index'])->name('referrals');
            Route::post('visits/{doctorVisit}/referrals/{kind}', [DoctorReferralController::class, 'store'])->whereIn('kind', ['referral', 'followup'])->middleware('throttle:20,1')->name('referrals.store');
            Route::get('referrals/{doctorReferral}', [DoctorReferralController::class, 'show'])->whereNumber('doctorReferral')->name('referrals.show');
            Route::get('referrals/{doctorReferral}/print', [DoctorDocumentPrintController::class, 'showReferral'])->whereNumber('doctorReferral')->name('referrals.print');
            Route::post('referrals/{doctorReferral}/print', [DoctorDocumentPrintController::class, 'storeReferral'])->whereNumber('doctorReferral')->middleware('throttle:20,1')->name('referrals.print.store');
            Route::post('referrals/{doctorReferral}/correction', [DoctorDocumentCorrectionController::class, 'referral'])->whereNumber('doctorReferral')->middleware('throttle:10,1')->name('referrals.correct');
            Route::put('referrals/{doctorReferral}', [DoctorReferralController::class, 'update'])->whereNumber('doctorReferral')->name('referrals.update');
        });

        Route::prefix('orders')->name('orders.')->group(function (): void {
            Route::get('lab', [DoctorDiagnosticOrderController::class, 'lab'])->name('lab');
            Route::get('radiology', [DoctorDiagnosticOrderController::class, 'radiology'])->name('radiology');
            Route::get('results', [DoctorExternalReportController::class, 'index'])->name('results');
            Route::get('reports/{doctorExternalReport}', [DoctorExternalReportController::class, 'show'])->whereNumber('doctorExternalReport')->name('reports.show');
            Route::post('reports/{doctorExternalReport}/review', [DoctorExternalReportController::class, 'review'])->whereNumber('doctorExternalReport')->middleware('throttle:20,1')->name('reports.review');
            Route::get('reports/{doctorExternalReport}/download', [DoctorExternalReportController::class, 'download'])->whereNumber('doctorExternalReport')->middleware('throttle:30,1')->name('reports.download');
            Route::post('{doctorDiagnosticOrder}/reports', [DoctorExternalReportController::class, 'store'])->whereNumber('doctorDiagnosticOrder')->middleware('throttle:10,1')->name('reports.store');
            Route::post('visits/{doctorVisit}/{kind}', [DoctorDiagnosticOrderController::class, 'store'])->whereIn('kind', ['lab', 'radiology'])->middleware('throttle:20,1')->name('store');
            Route::get('{doctorDiagnosticOrder}', [DoctorDiagnosticOrderController::class, 'show'])->whereNumber('doctorDiagnosticOrder')->name('show');
            Route::get('{doctorDiagnosticOrder}/print', [DoctorDocumentPrintController::class, 'showOrder'])->whereNumber('doctorDiagnosticOrder')->name('print');
            Route::post('{doctorDiagnosticOrder}/print', [DoctorDocumentPrintController::class, 'storeOrder'])->whereNumber('doctorDiagnosticOrder')->middleware('throttle:20,1')->name('print.store');
            Route::post('{doctorDiagnosticOrder}/correction', [DoctorDocumentCorrectionController::class, 'order'])->whereNumber('doctorDiagnosticOrder')->middleware('throttle:10,1')->name('correct');
            Route::put('{doctorDiagnosticOrder}', [DoctorDiagnosticOrderController::class, 'update'])->whereNumber('doctorDiagnosticOrder')->name('update');
        });

        Route::prefix('practice')->name('practice.')->group(function (): void {
            Route::get('services', [DoctorServiceController::class, 'index'])->name('services');
            Route::post('services', [DoctorServiceController::class, 'store'])->name('services.store');
            Route::put('services/{doctorService}', [DoctorServiceController::class, 'update'])->name('services.update');
            Route::patch('services/{doctorService}/status', [DoctorServiceController::class, 'updateStatus'])->name('services.status');
            Route::get('schedule', [DoctorScheduleController::class, 'index'])->name('schedule');
            Route::post('schedule', [DoctorScheduleController::class, 'store'])->name('schedule.store');
            Route::post('schedule/exceptions', [DoctorScheduleController::class, 'storeException'])->name('schedule.exceptions.store');
            Route::put('schedule/exceptions/{doctorScheduleException}', [DoctorScheduleController::class, 'updateException'])->name('schedule.exceptions.update');
            Route::delete('schedule/exceptions/{doctorScheduleException}', [DoctorScheduleController::class, 'destroyException'])->name('schedule.exceptions.destroy');
        });

        Route::get('finance/collections', [DoctorCollectionController::class, 'index'])->name('finance.collections');
        Route::post('finance/collections/{doctorAppointment}', [DoctorCollectionController::class, 'store'])->name('finance.collections.store');
        Route::post('finance/payments/{doctorPayment}/reverse', [DoctorCollectionController::class, 'reverse'])->name('finance.reverse');
        Route::get('finance/receipts/{doctorPayment}', [DoctorCollectionController::class, 'receipt'])->name('finance.receipt');
    });
});
