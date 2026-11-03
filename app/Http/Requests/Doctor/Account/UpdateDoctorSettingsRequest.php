<?php

namespace App\Http\Requests\Doctor\Account;

use App\Models\Doctor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDoctorSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user('doctor') instanceof Doctor;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'revision' => ['required', 'integer', 'min:0'],
            'tab' => ['required', Rule::in(['general', 'profile', 'availability', 'notifications', 'payment', 'stamps'])],
            'preferred_membership_id' => ['nullable', 'integer', Rule::exists('doctor_facilities', 'id')->where('doctor_id', $this->user('doctor')->id)->where('membership_status', 'active')],
            'availability' => ['required', Rule::in(array_keys(Doctor::AVAILABILITY))],
            'require_collection_before_visit' => ['sometimes', 'boolean'],
            'payment_policy' => ['sometimes', 'array:type,deposit_percent,remaining_timing,allow_early_payment'],
            'payment_policy.type' => ['required_with:payment_policy', Rule::in(array_keys(Doctor::PAYMENT_POLICY_TYPES))],
            'payment_policy.deposit_percent' => ['required_if:payment_policy.type,percentage', 'nullable', 'integer', 'between:1,99'],
            'payment_policy.remaining_timing' => ['required_if:payment_policy.type,none,percentage', Rule::in(array_keys(Doctor::PAYMENT_REMAINING_TIMINGS))],
            'payment_policy.allow_early_payment' => ['required_if:payment_policy.type,none,percentage', 'boolean'],
            'notifications' => ['required', 'array:appointments,arrival,results,referrals,payments'],
            ...collect(array_keys(Doctor::NOTIFICATION_PREFERENCES))->mapWithKeys(fn (string $key): array => ['notifications.'.$key => ['required', 'boolean']])->all(),
            'language' => ['prohibited'], 'theme' => ['prohibited'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['revision.required' => 'أعد فتح الإعدادات قبل الحفظ.', 'preferred_membership_id.exists' => 'اختر عضوية نشطة تابعة لحسابك.', 'availability.in' => 'اختر حالة توفر صحيحة.', 'notifications.*.boolean' => 'قيمة تفضيل التنبيه غير صحيحة.', 'prohibited' => 'هذا الخيار غير متاح للتعديل حاليًا.',
            'payment_policy.array' => 'بيانات سياسة الدفع غير صحيحة.',
            'payment_policy.type.required_with' => 'اختر المبلغ المطلوب عند الحجز.', 'payment_policy.type.in' => 'اختر سياسة دفع صحيحة.',
            'payment_policy.deposit_percent.required_if' => 'أدخل نسبة الدفعة المقدمة.', 'payment_policy.deposit_percent.integer' => 'نسبة الدفعة المقدمة يجب أن تكون عددًا صحيحًا.', 'payment_policy.deposit_percent.between' => 'نسبة الدفعة المقدمة يجب أن تكون بين 1 و99.',
            'payment_policy.remaining_timing.required_if' => 'اختر موعد دفع الرصيد المتبقي.', 'payment_policy.remaining_timing.in' => 'اختر موعد دفع صحيحًا.',
            'payment_policy.allow_early_payment.required_if' => 'حدد السماح بالدفع المبكر.', 'payment_policy.allow_early_payment.boolean' => 'قيمة السماح بالدفع المبكر غير صحيحة.'];
    }
}
