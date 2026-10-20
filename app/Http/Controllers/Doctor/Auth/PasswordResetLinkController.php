<?php

namespace App\Http\Controllers\Doctor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Auth\ForgotPasswordRequest;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create(): RedirectResponse
    {
        return redirect()->route('doctor.login', ['recovery' => 1]);
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $identity = $request->string('identity')->trim()->toString();
        $doctor = Doctor::query()
            ->where('email', $identity)
            ->orWhere('phone', preg_replace('/[\s()-]+/', '', $identity))
            ->first();

        if ($doctor !== null) {
            Password::broker('doctors')->sendResetLink(['email' => $doctor->email]);
        }

        return back()->with('recovery_status', 'إذا كانت البيانات مرتبطة بحساب طبيب، فسيصلك رابط الاستعادة خلال دقائق.');
    }
}
