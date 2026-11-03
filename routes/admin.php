<?php

use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\ApprovalDocumentController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\MedicationApprovalController;
use App\Http\Controllers\Admin\PatientDelegationController;
use App\Http\Controllers\Admin\PatientReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest:admin')->group(function (): void {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth:admin')->group(function (): void {
        Route::get('patient-reviews', [PatientReviewController::class, 'index'])->name('patient-reviews.index');
        Route::patch('patient-reviews/{review}', [PatientReviewController::class, 'update'])->name('patient-reviews.update');
        Route::get('patient-delegations', [PatientDelegationController::class, 'index'])->name('patient-delegations.index');
        Route::get('patient-delegations/{delegation}/proof', [PatientDelegationController::class, 'proof'])->name('patient-delegations.proof');
        Route::patch('patient-delegations/{delegation}', [PatientDelegationController::class, 'update'])->name('patient-delegations.update');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('doctors', DoctorController::class)->only(['index', 'show']);
        Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::patch('approvals/medications/{medication}', [MedicationApprovalController::class, 'update'])->name('approvals.medications.update');
        Route::get('approvals/credentials/{doctorCredential}/document', [ApprovalDocumentController::class, 'credential'])
            ->name('approvals.credentials.document');
        Route::get('approvals/achievements/{doctorAchievement}/evidence', [ApprovalDocumentController::class, 'achievement'])
            ->name('approvals.achievements.evidence');
        Route::get('approvals/verifications/{verificationRequest}/documents/{documentType}', [ApprovalDocumentController::class, 'verification'])
            ->name('approvals.verifications.document');
        Route::patch('approvals/{type}/{id}', [ApprovalController::class, 'update'])
            ->whereIn('type', ['specialty', 'credential', 'achievement', 'facility-membership', 'facility-proposal', 'doctor-verification'])
            ->whereNumber('id')
            ->name('approvals.update');
    });
});
