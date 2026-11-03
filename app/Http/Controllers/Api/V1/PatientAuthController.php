<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PatientAuthController extends Controller
{
    private function development(): bool
    {
        return app()->environment(['local', 'testing', 'staging']) && config('patient.development_verification');
    }

    private function patient(Request $request): Patient
    {
        return Patient::where('user_id', $request->user()->id)->where('is_active', true)->firstOrFail();
    }

    private function authenticated(User $user, string $device): JsonResponse
    {
        abort_unless(Patient::where('user_id', $user->id)->where('is_active', true)->exists(), 403);

        return response()->json(['data' => ['token' => $user->createToken($device, ['patient'], now()->addDays((int) config('patient.token_days', 30)))->plainTextToken]])->header('Cache-Control', 'no-store');
    }

    public function register(Request $request): JsonResponse
    {
        if (is_string($request->input('email'))) {
            $request->merge(['email' => mb_strtolower(trim($request->input('email')))]);
        }
        $data = $request->validate(['name' => ['required', 'string', 'max:150'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', 'confirmed', Password::min(10)], 'phone' => ['required', 'regex:/^\+[1-9][0-9]{7,14}$/'], 'terms_accepted' => ['accepted'], 'device_name' => ['required', 'string', 'max:100']]);
        $user = DB::transaction(function () use ($data): User {
            if (User::whereRaw('LOWER(email) = ?', [$data['email']])->exists()) {
                throw ValidationException::withMessages(['email' => 'هذا البريد مسجل بالفعل. سجل الدخول إلى حسابك.']);
            }
            $user = User::create(array_intersect_key($data, array_flip(['name', 'email', 'password'])));
            Patient::create(['user_id' => $user->id, 'name' => $data['name'], 'phone' => $data['phone'], 'booking_enabled' => false, 'is_active' => true]);

            return $user;
        });

        return $this->authenticated($user, $data['device_name']);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string'], 'device_name' => ['required', 'string', 'max:100']]);
        $user = User::whereRaw('LOWER(email) = ?', [mb_strtolower(trim($data['email']))])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'بيانات الدخول غير صحيحة.']);
        }

        return $this->authenticated($user, $data['device_name']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => ['user' => $request->user()->only('id', 'name', 'email'), 'patient' => $this->patient($request)->only('public_id', 'name', 'phone', 'phone_verified_at', 'birth_date', 'gender', 'address', 'health_information', 'booking_enabled'), 'development_verification' => $this->development()]])->header('Cache-Control', 'private, no-store');
    }

    public function sendCode(Request $request): JsonResponse
    {
        abort_unless($this->development(), 503, 'لم يتم تفعيل مزود توثيق الهاتف للإنتاج.');
        $patient = $this->patient($request);
        $code = (string) random_int(100000, 999999);
        Cache::put('patient-phone:'.$patient->id, ['hash' => Hash::make($code), 'phone' => $patient->phone], now()->addMinutes(5));

        return response()->json(['data' => ['development_code' => $code, 'expires_in' => 300]])->header('Cache-Control', 'no-store');
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => ['required', 'digits:6']]);
        $patient = $this->patient($request);
        $key = 'patient-phone:'.$patient->id;
        abort_if(RateLimiter::tooManyAttempts($key.':attempts', 5), 429);
        RateLimiter::hit($key.':attempts', 300);
        $ok = Cache::lock($key.':lock', 10)->block(3, function () use ($patient, $key, $data): bool {
            $stored = Cache::get($key);
            if ($patient->phone_verified_at || ! $stored || $stored['phone'] !== $patient->phone || ! Hash::check($data['code'], $stored['hash'])) {
                return false;
            }
            Cache::forget($key);
            $patient->update(['phone_verified_at' => now(), 'booking_enabled' => true]);

            return true;
        });
        if (! $ok) {
            throw ValidationException::withMessages(['code' => 'الرمز غير صحيح أو انتهت صلاحيته.']);
        }

        return $this->me($request);
    }

    public function profile(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => ['sometimes', 'required', 'string', 'max:150'], 'birth_date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'], 'gender' => ['nullable', 'in:male,female,unspecified'], 'address' => ['nullable', 'string', 'max:500'], 'health_information' => ['sometimes', 'array:blood_group,allergies,medications,family_history,emergency_contact'], 'health_information.*' => ['nullable', 'string', 'max:2000']]);
        $this->patient($request)->update($data);

        return $this->me($request);
    }

    public function password(Request $request): JsonResponse
    {
        $data = $request->validate(['current_password' => ['required', 'string'], 'password' => ['required', 'confirmed', Password::min(10)]]);
        $this->patient($request);
        if (! Hash::check($data['current_password'], $request->user()->password)) {
            throw ValidationException::withMessages(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }
        $request->user()->update(['password' => $data['password']]);
        $request->user()->tokens()->whereKeyNot($request->user()->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'تم تغيير كلمة المرور وإغلاق الجلسات الأخرى.']);
    }

    public function forgot(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        abort_unless($this->development(), 503, 'لم يتم تفعيل قناة استعادة الحساب للإنتاج.');
        $user = User::whereRaw('LOWER(email) = ?', [mb_strtolower(trim($data['email']))])->first();
        $result = ['message' => 'إذا كان الحساب مسجلاً ستتوفر تعليمات الاستعادة.'];
        if ($user && Patient::where('user_id', $user->id)->exists()) {
            $code = (string) random_int(100000, 999999);
            Cache::put('patient-reset:'.$user->id, Hash::make($code), now()->addMinutes(5));
            $result['development_code'] = $code;
        }

        return response()->json(['data' => $result])->header('Cache-Control', 'no-store');
    }

    public function reset(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'code' => ['required', 'digits:6'], 'password' => ['required', 'confirmed', Password::min(10)]]);
        $user = User::whereRaw('LOWER(email) = ?', [mb_strtolower(trim($data['email']))])->first();
        $key = 'patient-reset:'.($user?->id ?? 'missing');
        abort_if(RateLimiter::tooManyAttempts($key.':attempts', 5), 429);
        RateLimiter::hit($key.':attempts', 300);
        $ok = Cache::lock($key.':lock', 10)->block(3, function () use ($key, $data, $user): bool {
            $hash = Cache::get($key);
            if (! $user || ! $hash || ! Hash::check($data['code'], $hash)) {
                return false;
            }
            Cache::forget($key);
            $user->update(['password' => $data['password']]);
            $user->tokens()->delete();

            return true;
        });
        if (! $ok) {
            throw ValidationException::withMessages(['code' => 'الرمز غير صحيح أو انتهت صلاحيته.']);
        }

        return response()->json(['message' => 'تم تغيير كلمة المرور. سجل الدخول مجددًا.']);
    }

    public function sessions(Request $request): JsonResponse
    {
        $this->patient($request);

        return response()->json(['data' => $request->user()->tokens()->get(['id', 'name', 'last_used_at', 'expires_at'])]);
    }

    public function revokeSession(Request $request, int $id): JsonResponse
    {
        $this->patient($request);
        $request->user()->tokens()->findOrFail($id)->delete();

        return response()->json(['message' => 'تم إغلاق الجلسة.']);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج.']);
    }
}
