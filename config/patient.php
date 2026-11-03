<?php

return [
    'development_verification' => env('PATIENT_DEVELOPMENT_VERIFICATION', false),
    'token_days' => 30,
    'development_payments' => env('PATIENT_DEVELOPMENT_PAYMENTS', false),
    'fcm_project' => env('PATIENT_FCM_PROJECT'),
    'fcm_service_account' => env('PATIENT_FCM_SERVICE_ACCOUNT'),
];
