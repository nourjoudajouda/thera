<?php

namespace App\Http\Requests\Doctor\Account;

use App\Models\Doctor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorAchievementRequest extends FormRequest
{
    protected $errorBag = 'achievement';

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
            'type' => ['required', Rule::in(['award', 'accreditation', 'honor', 'professional'])],
            'title' => ['required', 'string', 'max:255'],
            'organization' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1950', 'max:'.now()->year],
            'description' => ['required', 'string', 'max:2000'],
            'evidence' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->string('title')->trim()->toString(),
            'organization' => $this->string('organization')->trim()->toString(),
            'description' => $this->string('description')->trim()->toString(),
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'type.required' => 'اختر نوع الإنجاز.',
            'type.in' => 'نوع الإنجاز المحدد غير صحيح.',
            'title.required' => 'أدخل عنوان الإنجاز.',
            'organization.required' => 'أدخل اسم الجهة.',
            'year.required' => 'أدخل سنة الإنجاز.',
            'year.min' => 'سنة الإنجاز المدخلة غير صحيحة.',
            'year.max' => 'سنة الإنجاز لا يمكن أن تكون في المستقبل.',
            'description.required' => 'أدخل وصفًا مختصرًا للإنجاز.',
            'evidence.required' => 'أرفق ملف إثبات الإنجاز.',
            'evidence.mimes' => 'يجب أن يكون ملف الإثبات PDF أو JPG أو PNG.',
            'evidence.extensions' => 'امتداد ملف الإثبات غير مسموح.',
            'evidence.max' => 'يجب ألا يزيد حجم ملف الإثبات عن 5 ميجابايت.',
        ];
    }
}
