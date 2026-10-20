<?php

namespace App\Http\Requests\Doctor\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    protected $errorBag = 'recovery';

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
            'identity' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return ['identity.required' => 'أدخل البريد الإلكتروني أو رقم الهاتف.'];
    }
}
