<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $authenticated = Auth::guard('admin')->attempt([
            'email' => $this->string('email')->trim()->lower()->toString(),
            'password' => $this->string('password')->toString(),
            'is_active' => true,
        ], $this->boolean('remember'));

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'بيانات تسجيل الدخول غير صحيحة أو الحساب غير فعّال.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "محاولات كثيرة جدًا. حاول مجددًا بعد {$seconds} ثانية.",
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate($this->string('email')->trim()->lower()).'|'.$this->ip();
    }

    public function messages(): array
    {
        return [
            'email.required' => 'أدخل البريد الإلكتروني.',
            'email.email' => 'أدخل بريدًا إلكترونيًا صحيحًا.',
            'password.required' => 'أدخل كلمة المرور.',
        ];
    }
}
