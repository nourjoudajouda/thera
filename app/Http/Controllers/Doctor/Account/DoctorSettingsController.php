<?php

namespace App\Http\Controllers\Doctor\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Account\UpdateDoctorSettingsRequest;
use App\Models\Doctor;
use App\Models\DoctorFacility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DoctorSettingsController extends Controller
{
    public function edit(Request $request): Response
    {
        $request->validate(['tab' => ['nullable', Rule::in(['general', 'profile', 'availability', 'notifications', 'payment', 'stamps'])]]);
        $doctor = $request->user('doctor')->fresh();
        $doctor->load('primarySpecialtyAssignment.specialty');
        $memberships = $doctor->facilityMemberships()->where('membership_status', DoctorFacility::STATUS_ACTIVE)->whereHas('facility', fn ($query) => $query->where('is_active', true))->with(['facility', 'services' => fn ($query) => $query->where('is_active', true)->select('id', 'doctor_facility_id', 'name')])->get();
        $preferences = $doctor->accountPreferences();
        $tab = $request->input('tab', 'general');
        $stamps = $doctor->stamps()->latest('id')->get();

        return response()->view('doctor.account.settings', compact('doctor', 'memberships', 'preferences', 'tab', 'stamps'))->header('Cache-Control', 'private, no-store');
    }

    public function update(UpdateDoctorSettingsRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($request, $data): void {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $before = $doctor->accountPreferences();
            $paymentPolicy = array_replace($before['payment_policy'], $data['payment_policy'] ?? []);
            $paymentPolicy['deposit_percent'] = (int) ($paymentPolicy['deposit_percent'] ?? $before['payment_policy']['deposit_percent']);
            $paymentPolicy['allow_early_payment'] = (bool) $paymentPolicy['allow_early_payment'];
            $requireCollection = $paymentPolicy['type'] === 'full' || (isset($data['payment_policy'])
                ? $paymentPolicy['remaining_timing'] !== 'after_visit'
                : (bool) ($data['require_collection_before_visit'] ?? $before['require_collection_before_visit']));
            if (! isset($data['payment_policy']) && array_key_exists('require_collection_before_visit', $data)) {
                $paymentPolicy['remaining_timing'] = $requireCollection ? 'before_visit' : 'after_visit';
            }
            $after = [
                'preferred_membership_id' => empty($data['preferred_membership_id']) ? null : (int) $data['preferred_membership_id'],
                'availability' => $data['availability'],
                'notifications' => collect(array_keys(Doctor::NOTIFICATION_PREFERENCES))->mapWithKeys(fn (string $key): array => [$key => (bool) $data['notifications'][$key]])->all(),
                'require_collection_before_visit' => $requireCollection,
                'payment_policy' => $paymentPolicy,
            ];
            if ($after['preferred_membership_id'] && ! $doctor->facilityMemberships()->whereKey($after['preferred_membership_id'])->where('membership_status', DoctorFacility::STATUS_ACTIVE)->whereHas('facility', fn ($query) => $query->where('is_active', true))->exists()) {
                throw ValidationException::withMessages(['preferred_membership_id' => 'العضوية أو المنشأة غير نشطة؛ اختر منشأة أخرى.']);
            }
            if ($before === $after) {
                return;
            }
            if ($doctor->preferences_revision !== (int) $data['revision']) {
                throw ValidationException::withMessages(['revision' => 'تغيرت إعداداتك من نافذة أخرى؛ أعد فتح الصفحة قبل الحفظ.']);
            }
            $doctor->account_preferences = $after;
            $doctor->preferences_revision++;
            $doctor->save();
            DB::table('doctor_setting_events')->insert(['doctor_id' => $doctor->id, 'revision' => $doctor->preferences_revision, 'before' => json_encode($before, JSON_THROW_ON_ERROR), 'after' => json_encode($after, JSON_THROW_ON_ERROR), 'created_at' => now('UTC')]);
        }, 3);
        $message = 'حُفظت إعدادات حسابك على الخادم. تفضيلات التنبيهات لا تعني تفعيل الإرسال الخارجي.';
        $url = route('doctor.account.settings', ['tab' => $data['tab']]);

        return $request->expectsJson() ? response()->json(compact('message', 'url')) : redirect($url)->with('status', $message);
    }
}
