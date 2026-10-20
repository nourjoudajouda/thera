<?php

namespace App\Http\Requests\Doctor\Auth;

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
            'identity' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identity = $this->string('identity')->trim()->toString();
        $identityField = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (! Auth::guard('doctor')->attempt([
            $identityField => $identity,
            'password' => $this->string('password')->toString(),
        ], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identity' => 'بيانات تسجيل الدخول غير صحيحة.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identity' => "محاولات كثيرة جدًا. حاول مجددًا بعد {$seconds} ثانية.",
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('identity')->trim())).'|'.$this->ip();
    }
}
