<?php

namespace App\Http\Requests\Doctor\Account;

use App\Models\Doctor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorCredentialRequest extends FormRequest
{
    protected $errorBag = 'credential';

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
            'type' => ['required', Rule::in(['qualification', 'certificate', 'fellowship', 'board'])],
            'title' => ['required', 'string', 'max:255'],
            'issuer' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:100'],
            'issued_on' => ['required', 'date', 'before_or_equal:today'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:issued_on'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->string('title')->trim()->toString(),
            'issuer' => $this->string('issuer')->trim()->toString(),
            'country' => $this->string('country')->trim()->toString(),
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'type.required' => 'اختر نوع المؤهل.',
            'type.in' => 'نوع المؤهل المحدد غير صحيح.',
            'title.required' => 'أدخل اسم المؤهل أو الشهادة.',
            'issuer.required' => 'أدخل اسم الجهة المصدرة.',
            'country.required' => 'أدخل الدولة.',
            'issued_on.required' => 'أدخل تاريخ الإصدار.',
            'issued_on.before_or_equal' => 'تاريخ الإصدار لا يمكن أن يكون في المستقبل.',
            'expires_on.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ الإصدار.',
            'document.required' => 'أرفق ملف إثبات المؤهل.',
            'document.mimes' => 'يجب أن يكون ملف الإثبات PDF أو JPG أو PNG.',
            'document.extensions' => 'امتداد ملف الإثبات غير مسموح.',
            'document.max' => 'يجب ألا يزيد حجم ملف الإثبات عن 5 ميجابايت.',
        ];
    }
}
