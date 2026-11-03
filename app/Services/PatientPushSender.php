<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientDevice;
use App\Models\PatientNotification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PatientPushSender
{
    private function encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function accessToken(): string
    {
        $path = config('patient.fcm_service_account');
        if (! is_string($path) || ! is_file($path)) {
            throw new \RuntimeException('FCM service account is not configured.');
        }

        return Cache::remember('thera-fcm-token:'.hash('sha256', $path), 3000, function () use ($path): string {
            $account = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            $header = $this->encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
            $claims = $this->encode(json_encode(['iss' => $account['client_email'], 'scope' => 'https://www.googleapis.com/auth/firebase.messaging', 'aud' => 'https://oauth2.googleapis.com/token', 'iat' => time(), 'exp' => time() + 3600], JSON_THROW_ON_ERROR));
            if (! openssl_sign($header.'.'.$claims, $signature, $account['private_key'], OPENSSL_ALGO_SHA256)) {
                throw new \RuntimeException('FCM credentials could not be signed.');
            }
            $result = Http::asForm()->timeout(20)->post('https://oauth2.googleapis.com/token', ['grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer', 'assertion' => $header.'.'.$claims.'.'.$this->encode($signature)]);
            if (! $result->successful() || ! is_string($result->json('access_token'))) {
                throw new \RuntimeException('FCM authentication failed.');
            }

            return $result->json('access_token');
        });
    }

    public function send(PatientNotification $notification): void
    {
        $user = User::find($notification->user_id);
        if (! $user || ! Patient::accessibleTo($user)->whereKey($notification->patient_id)->exists()) {
            $notification->update(['push_state' => 'skipped']);

            return;
        }
        $project = config('patient.fcm_project');
        if (! $project || ! config('patient.fcm_service_account')) {
            return;
        }
        $devices = PatientDevice::where('user_id', $user->id)->where('enabled', true)->whereIn('personal_access_token_id', $user->tokens()->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->select('id'))->get();
        if ($devices->isEmpty()) {
            $notification->update(['push_state' => 'skipped']);

            return;
        }
        $token = $this->accessToken();
        foreach ($devices as $device) {
            $result = Http::withToken($token)->timeout(20)->post('https://fcm.googleapis.com/v1/projects/'.rawurlencode($project).'/messages:send', ['message' => ['token' => $device->token, 'notification' => ['title' => 'THERA', 'body' => $device->locale === 'en' ? 'There is an update in your THERA account.' : 'يوجد تحديث في حسابك على THERA.'], 'data' => ['notification_id' => (string) $notification->id], 'android' => ['notification' => ['channel_id' => 'thera_updates']]]]);
            if (! $result->successful()) {
                if ($result->json('error.details.0.errorCode') === 'UNREGISTERED') {
                    $device->delete();

                    continue;
                }
                throw new \RuntimeException('FCM delivery failed with HTTP '.$result->status());
            }
        }
        $notification->update(['push_state' => 'sent']);
    }
}
