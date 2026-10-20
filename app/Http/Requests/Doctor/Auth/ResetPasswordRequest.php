<?php

namespace App\Http\Requests\Doctor\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'password.required' => 'أدخل كلمة المرور الجديدة.',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
        ];
    }
}
