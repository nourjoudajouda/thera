<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $admin = $request->user('admin');

        if ($admin instanceof Admin) {
            $admin->forceFill(['last_login_at' => now()])->save();
        }

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
