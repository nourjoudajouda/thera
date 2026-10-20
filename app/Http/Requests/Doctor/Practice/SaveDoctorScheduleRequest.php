<?php

namespace App\Http\Requests\Doctor\Practice;

use App\Models\Doctor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveDoctorScheduleRequest extends FormRequest
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
            'doctor_facility_id' => ['required', 'integer'],
            'revision' => ['required', 'integer', 'min:0'],
            'daily_capacity' => ['required', 'integer', 'min:0', 'max:500'],
            'is_active' => ['required', 'boolean'],
            ...self::periodRules(),
        ];
    }

    /** @return array<string, mixed> */
    public static function periodRules(): array
    {
        return [
            'periods' => ['present', 'array', 'max:28'],
            'periods.*' => ['array:weekday,start,end,breaks,service_ids'],
            'periods.*.weekday' => ['required', 'integer', 'between:0,6'],
            'periods.*.start' => ['required', 'date_format:H:i'],
            'periods.*.end' => ['required', 'date_format:H:i'],
            'periods.*.service_ids' => ['required', 'array', 'min:1', 'max:100'],
            'periods.*.service_ids.*' => ['required', 'integer'],
            'periods.*.breaks' => ['present', 'array', 'max:10'],
            'periods.*.breaks.*' => ['array:start,end'],
            'periods.*.breaks.*.start' => ['required', 'date_format:H:i'],
            'periods.*.breaks.*.end' => ['required', 'date_format:H:i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('periods_payload')) {
            $raw = $this->input('periods_payload');
            $this->merge(['periods' => is_string($raw) ? json_decode($raw, true) : null]);
        }
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'required' => 'أكمل حقل :attribute.',
            'present' => 'أكمل بيانات :attribute.',
            'integer' => 'حقل :attribute يجب أن يكون عددًا صحيحًا.',
            'array' => 'بيانات :attribute غير صالحة.',
            'date_format' => 'أدخل وقتًا صحيحًا بصيغة ساعة:دقيقة.',
            'daily_capacity.min' => 'السعة اليومية لا تقل عن صفر.',
            'daily_capacity.max' => 'السعة اليومية لا تزيد عن 500 مريض.',
            'periods.max' => 'الحد الأقصى 28 فترة في الأسبوع.',
            'periods.*.weekday.between' => 'اليوم المحدد غير صالح.',
            'periods.*.service_ids.min' => 'اختر خدمة واحدة على الأقل للفترة.',
            'periods.*.breaks.max' => 'الحد الأقصى 10 استراحات في الفترة.',
            'is_active.boolean' => 'حالة الجدول غير صالحة.',
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return ['doctor_facility_id' => 'المنشأة', 'revision' => 'نسخة الجدول', 'daily_capacity' => 'السعة اليومية', 'is_active' => 'تفعيل الجدول', 'periods' => 'فترات الدوام', 'periods.*.start' => 'بداية الفترة', 'periods.*.end' => 'نهاية الفترة', 'periods.*.service_ids' => 'خدمات الفترة', 'periods.*.breaks' => 'الاستراحات'];
    }
}
