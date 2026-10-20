<?php

namespace App\Http\Controllers\Doctor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Auth\ResetPasswordRequest;
use App\Models\Doctor;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(string $token): View
    {
        return view('doctor.auth.reset-password', [
            'token' => $token,
            'email' => request()->string('email')->toString(),
        ]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('doctors')->reset(
            $request->safe()->only(['email', 'password', 'password_confirmation', 'token']),
            function (Doctor $doctor, string $password): void {
                $doctor->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($doctor));
            },
        );

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages(['email' => 'رابط الاستعادة غير صالح أو منتهي.']);
        }

        return redirect()->route('doctor.login')->with('status', 'تم تحديث كلمة المرور. يمكنك تسجيل الدخول الآن.');
    }
}
