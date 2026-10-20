<?php

namespace App\Http\Requests\Doctor\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterDoctorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:doctors,email'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/', 'max:30', 'unique:doctors,phone'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'phone' => preg_replace('/[\s()-]+/', '', (string) $this->input('phone')),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'أدخل الاسم الكامل.',
            'email.required' => 'أدخل البريد الإلكتروني.',
            'email.email' => 'أدخل بريدًا إلكترونيًا صحيحًا.',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقًا.',
            'phone.required' => 'أدخل رقم الهاتف.',
            'phone.regex' => 'أدخل رقم هاتف صحيحًا مع رمز الدولة.',
            'phone.unique' => 'رقم الهاتف مستخدم مسبقًا.',
            'password.required' => 'أدخل كلمة المرور.',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'terms.accepted' => 'يجب الموافقة على الشروط وسياسة الخصوصية.',
        ];
    }
}
