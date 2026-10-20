<?php

namespace App\Http\Controllers\Doctor\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MedicationController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user('doctor')->verification_status !== 'verified') {
            throw ValidationException::withMessages(['name' => 'اقتراح دواء يتطلب حساب طبيب موثقًا.']);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'active_ingredient' => ['required', 'string', 'max:150'],
            'strength' => ['required', 'string', 'max:100'],
            'dosage_form' => ['required', 'string', 'max:100'],
        ], ['required' => 'أكمل تعريف الدواء حتى تتمكن الإدارة من مراجعته.']);
        $fingerprint = Medication::fingerprintFor($data);
        $existing = Medication::query()->where('fingerprint', $fingerprint)->first();
        if ($existing && $existing->status !== 'approved' && $existing->doctor_id !== $request->user('doctor')->id) {
            throw ValidationException::withMessages(['name' => 'هذا التعريف قيد مراجعة الإدارة. لا يمكن إضافته مجددًا حاليًا.']);
        }
        Medication::query()->firstOrCreate(['fingerprint' => $fingerprint], $data + ['doctor_id' => $request->user('doctor')->id, 'status' => 'pending']);
        $message = 'حُفظ تعريف الدواء. يمكنك استخدامه في المسودة، ولا يُعتمد في وصفة قبل مراجعة الإدارة.';

        return $request->expectsJson() ? response()->json(compact('message')) : back()->with('status', $message);
    }
}
