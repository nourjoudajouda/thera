<?php

namespace App\Http\Requests\Doctor\Account;

use App\Models\Doctor;
use App\Models\DoctorVerificationRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVerificationDocumentRequest extends FormRequest
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
            'document_type' => [
                'required',
                Rule::in(array_keys(DoctorVerificationRequest::requiredDocumentLabels())),
            ],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'extensions:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'document_type.required' => 'حدد نوع المستند.',
            'document_type.in' => 'نوع المستند غير صالح.',
            'document.required' => 'اختر ملف المستند.',
            'document.file' => 'الملف المرفوع غير صالح.',
            'document.mimes' => 'صيغة المستند يجب أن تكون PDF أو JPG أو PNG.',
            'document.extensions' => 'امتداد المستند يجب أن يكون PDF أو JPG أو PNG.',
            'document.max' => 'حجم المستند يجب ألا يتجاوز 5 ميجابايت.',
        ];
    }
}
