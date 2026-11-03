<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientDelegation;
use App\Models\PatientDoctorConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PatientFamilyController extends Controller
{
    public function profiles(Request $request): JsonResponse
    {
        $profiles = Patient::accessibleTo($request->user())->orderBy('id')->get();
        foreach ($profiles as $patient) {
            $patient->recordAccess('profile_listed', $request->user()->id);
        }

        return response()->json(['data' => $profiles->map(fn ($p) => [...$p->only('public_id', 'name', 'phone', 'birth_date', 'booking_enabled'), 'is_self' => $p->user_id === $request->user()->id])])->header('Cache-Control', 'private, no-store');
    }

    public function profile(Request $request, string $patient): JsonResponse
    {
        $record = Patient::accessibleTo($request->user())->where('public_id', $patient)->firstOrFail();
        $record->recordAccess('profile_read', $request->user()->id);

        return response()->json(['data' => $record->only('public_id', 'name', 'phone', 'birth_date', 'gender', 'address', 'health_information', 'booking_enabled')])->header('Cache-Control', 'private, no-store');
    }

    public function updateProfile(Request $request, string $patient): JsonResponse
    {
        $record = Patient::accessibleTo($request->user())->where('public_id', $patient)->firstOrFail();
        $data = $request->validate(['name' => ['sometimes', 'required', 'string', 'max:150'], 'birth_date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'], 'gender' => ['nullable', 'in:male,female,unspecified'], 'address' => ['nullable', 'string', 'max:500'], 'health_information' => ['sometimes', 'array:blood_group,allergies,medications,family_history,emergency_contact'], 'health_information.*' => ['nullable', 'string', 'max:2000']]);
        if ($record->user_id !== $request->user()->id) {
            unset($data['name'],$data['birth_date'],$data['gender']);
        }
        $record->update($data);
        $record->recordAccess('profile_updated', $request->user()->id);

        return $this->profile($request, $patient);
    }

    public function index(Request $request): JsonResponse
    {
        $records = PatientDelegation::where(fn ($q) => $q->where('user_id', $request->user()->id)->orWhereHas('patient', fn ($q) => $q->where('user_id', $request->user()->id)))->with(['patient', 'user'])->orderByDesc('id')->get();

        return response()->json(['data' => $records->map(fn ($d) => [...$d->only('id', 'kind', 'status', 'relationship', 'expires_at', 'review_reason'), 'patient_reference' => $d->patient->reference(), 'patient_name' => $d->kind === 'guardian' || $d->status === 'approved' || $d->patient->user_id === $request->user()->id ? $d->patient->name : null, 'requester_name' => $d->user->name, 'can_approve' => $d->kind === 'adult' && $d->patient->user_id === $request->user()->id && $d->status === 'pending'])])->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request): JsonResponse
    {
        $owner = Patient::where('user_id', $request->user()->id)->where('is_active', true)->whereNotNull('phone_verified_at')->firstOrFail();
        $data = $request->validate(['kind' => ['required', 'in:guardian,adult'], 'relationship' => ['required', 'string', 'max:100'], 'target_public_id' => ['nullable', 'string', 'max:30', 'required_if:kind,adult'], 'name' => ['required_without:target_public_id', 'string', 'max:150'], 'birth_date' => ['required_without:target_public_id', 'date_format:Y-m-d', 'before:today'], 'proof' => ['required_if:kind,guardian', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']]);
        $proof = $request->file('proof')?->store('patient-delegation-proofs', 'local');
        try {
            $grant = DB::transaction(function () use ($request, $data, $proof, $owner): PatientDelegation {
                $patient = ! empty($data['target_public_id']) ? Patient::where('public_id', preg_replace('/^TH-/', '', $data['target_public_id']))->where('is_active', true)->lockForUpdate()->firstOrFail() : Patient::create(['name' => $data['name'], 'birth_date' => $data['birth_date'], 'phone' => $owner->phone, 'booking_enabled' => false, 'is_active' => true]);
                abort_if($patient->user_id === $request->user()->id || ($data['kind'] === 'adult' && ! $patient->user_id), 422, 'اختر ملف شخص آخر يملك حسابًا للموافقة.');
                abort_if($data['kind'] === 'guardian' && (! $patient->birth_date || $patient->birth_date->age >= 18), 422, 'تفويض البالغ يتطلب موافقته من حسابه؛ مسار الولاية مخصص لمن لم يتم 18 عامًا.');
                $existing = PatientDelegation::where('patient_id', $patient->id)->where('user_id', $request->user()->id)->first();
                abort_if($existing && in_array($existing->status, ['pending', 'approved'], true), 409, 'يوجد طلب أو تفويض قائم لهذا الملف.');
                $grant = PatientDelegation::updateOrCreate(['patient_id' => $patient->id, 'user_id' => $request->user()->id], ['kind' => $data['kind'], 'relationship' => $data['relationship'], 'status' => 'pending', 'proof_path' => $proof, 'reviewed_by' => null, 'review_reason' => null, 'expires_at' => null]);
                $patient->recordAccess('delegation_requested', $request->user()->id, detail: ['delegation_id' => $grant->id, 'kind' => $grant->kind]);

                return $grant;
            });
        } catch (\Throwable $error) {
            if ($proof) {
                Storage::disk('local')->delete($proof);
            }
            throw $error;
        }

        return response()->json(['data' => $grant->only('id', 'status')], 201);
    }

    public function update(Request $request, PatientDelegation $delegation): JsonResponse
    {
        $data = $request->validate(['action' => ['required', 'in:approve,reject,revoke'], 'expires_at' => ['required_if:action,approve', 'date', 'after:now', 'before_or_equal:'.now()->addYear()->toDateString()]]);
        DB::transaction(function () use ($request, $delegation, $data): void {
            $patient = Patient::lockForUpdate()->findOrFail($delegation->patient_id);
            $delegation->refresh();
            $owner = $patient->user_id === $request->user()->id;
            abort_unless($owner || ($delegation->user_id === $request->user()->id && $data['action'] === 'revoke'), 404);
            if ($data['action'] !== 'revoke') {
                abort_unless($owner && $delegation->kind === 'adult' && $delegation->status === 'pending', 409);
            }
            $delegation->update(['status' => ['approve' => 'approved', 'reject' => 'rejected', 'revoke' => 'revoked'][$data['action']], 'expires_at' => $data['action'] === 'approve' ? $data['expires_at'] : $delegation->expires_at]);
            $patient->recordAccess('delegation_'.$data['action'], $request->user()->id, detail: ['delegation_id' => $delegation->id]);
        });

        return response()->json(['data' => $delegation->only('id', 'status', 'expires_at')]);
    }

    public function consents(Request $request, string $patient): JsonResponse
    {
        $record = Patient::accessibleTo($request->user())->where('public_id', $patient)->firstOrFail();

        return response()->json(['data' => PatientDoctorConsent::where('patient_id', $record->id)->get()->map(fn ($c) => [...$c->only('id', 'doctor_id', 'expires_at', 'revoked_at'), 'doctor_name' => Doctor::find($c->doctor_id)?->name])])->header('Cache-Control', 'private, no-store');
    }

    public function consent(Request $request, string $patient): JsonResponse
    {
        $record = Patient::accessibleTo($request->user())->where('public_id', $patient)->firstOrFail();
        $data = $request->validate(['doctor_id' => ['required', 'integer', 'exists:doctors,id'], 'action' => ['required', 'in:grant,revoke']]);
        DB::transaction(function () use ($record, $data, $request): void {
            $record->newQuery()->whereKey($record->id)->lockForUpdate()->firstOrFail();
            PatientDoctorConsent::updateOrCreate(['patient_id' => $record->id, 'doctor_id' => $data['doctor_id']], ['granted_by' => $request->user()->id, 'expires_at' => now()->addDays(30), 'revoked_at' => $data['action'] === 'revoke' ? now() : null]);
            $record->recordAccess('doctor_consent_'.$data['action'], $request->user()->id, $data['doctor_id']);
        });

        return $this->consents($request, $patient);
    }
}
