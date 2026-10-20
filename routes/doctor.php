<?php

use App\Http\Controllers\Doctor\Account\DoctorAchievementController;
use App\Http\Controllers\Doctor\Account\DoctorCredentialController;
use App\Http\Controllers\Doctor\Account\DoctorFacilityController;
use App\Http\Controllers\Doctor\Account\DoctorFacilityProposalController;
use App\Http\Controllers\Doctor\Account\DoctorSpecialtyController;
use App\Http\Controllers\Doctor\Account\ProfessionalProfileController;
use App\Http\Controllers\Doctor\Account\VerificationController;
use App\Http\Controllers\Doctor\Appointments\DoctorAppointmentController;
use App\Http\Controllers\Doctor\Appointments\DoctorQueueController;
use App\Http\Controllers\Doctor\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Doctor\Auth\NewPasswordController;
use App\Http\Controllers\Doctor\Auth\PasswordResetLinkController;
use App\Http\Controllers\Doctor\Auth\RegisteredDoctorController;
use App\Http\Controllers\Doctor\Clinical\DoctorPrescriptionController;
use App\Http\Controllers\Doctor\Clinical\DoctorVisitController;
use App\Http\Controllers\Doctor\Finance\DoctorCollectionController;
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
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::view('/', 'doctor.dashboard')->name('dashboard');

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
            Route::view('settings', 'doctor.account.settings')->name('settings');
        });

        Route::prefix('appointments')->name('appointments.')->group(function (): void {
            Route::get('/', [DoctorAppointmentController::class, 'index'])->name('index');
            Route::get('create', [DoctorAppointmentController::class, 'create'])->name('create');
            Route::get('details', [DoctorAppointmentController::class, 'show'])->name('show');
            Route::get('availability', [DoctorAppointmentController::class, 'availability'])->name('availability');
            Route::get('contacts', [DoctorAppointmentController::class, 'contacts'])->name('contacts');
            Route::post('/', [DoctorAppointmentController::class, 'store'])->name('store');
            Route::get('{doctorAppointment}/edit', [DoctorAppointmentController::class, 'edit'])->name('edit');
            Route::put('{doctorAppointment}', [DoctorAppointmentController::class, 'update'])->name('update');
            Route::patch('{doctorAppointment}/status', [DoctorAppointmentController::class, 'status'])->name('status');
            Route::post('{doctorAppointment}/queue', [DoctorQueueController::class, 'store'])->name('queue.store');
        });

        Route::prefix('patients')->name('patients.')->group(function (): void {
            Route::view('/', 'doctor.patients.index')->name('index');
            Route::view('details', 'doctor.patients.show')->name('show');
        });

        Route::prefix('clinical')->name('clinical.')->group(function (): void {
            Route::get('visits', [DoctorVisitController::class, 'index'])->name('visits');
            Route::get('visit', [DoctorVisitController::class, 'show'])->name('visit');
            Route::post('visits/{doctorAppointment}/start', [DoctorVisitController::class, 'store'])->name('visits.start');
            Route::put('visits/{doctorVisit}', [DoctorVisitController::class, 'update'])->name('visits.update');
            Route::get('prescriptions', [DoctorPrescriptionController::class, 'index'])->name('prescriptions');
            Route::get('prescriptions/{doctorPrescription}', [DoctorPrescriptionController::class, 'show'])->name('prescriptions.show');
            Route::put('prescriptions/{doctorPrescription}', [DoctorPrescriptionController::class, 'update'])->name('prescriptions.update');
            Route::post('visits/{doctorVisit}/prescription', [DoctorPrescriptionController::class, 'store'])->name('prescriptions.store');
            Route::view('referrals', 'doctor.clinical.referrals')->name('referrals');
        });

        Route::prefix('orders')->name('orders.')->group(function (): void {
            Route::view('lab', 'doctor.orders.lab')->name('lab');
            Route::view('radiology', 'doctor.orders.radiology')->name('radiology');
            Route::view('results', 'doctor.orders.results')->name('results');
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
