<?php

namespace App\Http\Controllers\Doctor\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\Auth\RegisterDoctorRequest;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredDoctorController extends Controller
{
    public function create(): View
    {
        return view('doctor.auth.register');
    }

    public function store(RegisterDoctorRequest $request): RedirectResponse
    {
        $doctor = Doctor::query()->create([
            ...$request->safe()->only(['name', 'email', 'phone', 'password']),
            'verification_status' => 'profile_incomplete',
            'terms_accepted_at' => now(),
        ]);

        Auth::guard('doctor')->login($doctor);
        $request->session()->regenerate();

        return redirect()->route('doctor.account.professional-profile', ['onboarding' => 1]);
    }
}
