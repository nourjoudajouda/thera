<?php

use App\Http\Controllers\Api\V1\PatientAuthController;
use App\Http\Controllers\Api\V1\PatientBookingController;
use App\Http\Controllers\Api\V1\PatientEngagementController;
use App\Http\Controllers\Api\V1\PatientFamilyController;
use App\Http\Controllers\Api\V1\PatientRecordController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->middleware('throttle:patient-api')->group(function (): void {
    Route::post('auth/register', [PatientAuthController::class, 'register'])->middleware('throttle:patient-sensitive');
    Route::post('auth/login', [PatientAuthController::class, 'login'])->middleware('throttle:patient-sensitive');
    Route::post('auth/forgot-password', [PatientAuthController::class, 'forgot'])->middleware('throttle:patient-sensitive');
    Route::post('auth/reset-password', [PatientAuthController::class, 'reset'])->middleware('throttle:patient-sensitive');
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('notifications', [PatientEngagementController::class, 'notifications']);
        Route::post('notifications/{id}/read', [PatientEngagementController::class, 'readNotification']);
        Route::post('devices', [PatientEngagementController::class, 'device']);
        Route::get('favorites', [PatientEngagementController::class, 'favorites']);
        Route::put('favorites/{doctor}', [PatientEngagementController::class, 'favorite']);
        Route::delete('favorites/{doctor}', [PatientEngagementController::class, 'favorite']);
        Route::get('patients/{patient}/reminders', [PatientEngagementController::class, 'reminders']);
        Route::post('patients/{patient}/reminders', [PatientEngagementController::class, 'reminder']);
        Route::put('patients/{patient}/reminders/{reminder}', [PatientEngagementController::class, 'reminder']);
        Route::delete('patients/{patient}/reminders/{reminder}', [PatientEngagementController::class, 'reminder']);
        Route::put('patients/{patient}/appointments/{appointment}/review', [PatientEngagementController::class, 'review']);
        Route::get('patients', [PatientFamilyController::class, 'profiles']);
        Route::get('patients/{patient}/records', [PatientRecordController::class, 'index']);
        Route::get('patients/{patient}/records/{type}/{id}', [PatientRecordController::class, 'show'])->whereIn('type', ['visit', 'prescription', 'order', 'referral', 'report']);
        Route::get('patients/{patient}/records/{type}/{id}/pdf', [PatientRecordController::class, 'pdf'])->whereIn('type', ['visit', 'prescription', 'order', 'referral']);
        Route::get('patients/{patient}/reports/{id}/file', [PatientRecordController::class, 'externalReport']);
        Route::get('patients/{patient}/profile', [PatientFamilyController::class, 'profile']);
        Route::put('patients/{patient}/profile', [PatientFamilyController::class, 'updateProfile']);
        Route::get('family/delegations', [PatientFamilyController::class, 'index']);
        Route::post('family/delegations', [PatientFamilyController::class, 'store']);
        Route::patch('family/delegations/{delegation}', [PatientFamilyController::class, 'update']);
        Route::get('patients/{patient}/consents', [PatientFamilyController::class, 'consents']);
        Route::post('patients/{patient}/consents', [PatientFamilyController::class, 'consent']);
        Route::get('doctors', [PatientBookingController::class, 'doctors']);
        Route::get('doctor-filters', [PatientBookingController::class, 'filters']);
        Route::get('services/{service}/availability', [PatientBookingController::class, 'availability']);
        Route::get('patients/{patient}/appointments', [PatientBookingController::class, 'index']);
        Route::post('patients/{patient}/appointments', [PatientBookingController::class, 'store']);
        Route::get('patients/{patient}/appointments/{appointment}', [PatientBookingController::class, 'show']);
        Route::patch('patients/{patient}/appointments/{appointment}', [PatientBookingController::class, 'change']);
        Route::post('patients/{patient}/appointments/{appointment}/payments', [PatientBookingController::class, 'pay']);
        Route::post('patients/{patient}/appointments/{appointment}/payments/resolve', [PatientBookingController::class, 'resolve']);
        Route::post('patients/{patient}/appointments/{appointment}/check-in', [PatientBookingController::class, 'checkIn']);
        Route::get('auth/me', [PatientAuthController::class, 'me']);
        Route::post('auth/logout', [PatientAuthController::class, 'logout']);
        Route::get('auth/sessions', [PatientAuthController::class, 'sessions']);
        Route::delete('auth/sessions/{id}', [PatientAuthController::class, 'revokeSession']);
        Route::put('auth/password', [PatientAuthController::class, 'password']);
        Route::post('auth/phone/code', [PatientAuthController::class, 'sendCode'])->middleware('throttle:patient-sensitive');
        Route::post('auth/phone/verify', [PatientAuthController::class, 'verifyCode'])->middleware('throttle:patient-sensitive');
        Route::put('patient/profile', [PatientAuthController::class, 'profile']);
    });
});
