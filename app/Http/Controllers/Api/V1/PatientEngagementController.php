<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorPrescription;
use App\Models\Patient;
use App\Models\PatientDevice;
use App\Models\PatientFavorite;
use App\Models\PatientNotification;
use App\Models\PatientReminder;
use App\Models\PatientReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;

class PatientEngagementController extends Controller
{
    private function patient(Request $request): Patient
    {
        return Patient::accessibleTo($request->user())->where('public_id', $request->route('patient'))->firstOrFail();
    }

    public function notifications(Request $request): JsonResponse
    {
        $records = PatientNotification::where('user_id', $request->user()->id)->whereHas('patient', fn ($q) => $q->accessibleTo($request->user()))->with('patient')->orderByDesc('id')->paginate(30);

        return response()->json(['data' => $records->getCollection()->map(fn ($n) => [...$n->only('id', 'kind', 'target_type', 'target_id', 'created_at', 'read_at'), 'patient_public_id' => $n->patient->public_id, 'patient_name' => $n->patient->name]), 'meta' => ['current_page' => $records->currentPage(), 'last_page' => $records->lastPage()]])->header('Cache-Control', 'private, no-store');
    }

    public function readNotification(Request $request, int $id): JsonResponse
    {
        $notification = PatientNotification::where('user_id', $request->user()->id)->whereHas('patient', fn ($q) => $q->accessibleTo($request->user()))->findOrFail($id);
        $notification->update(['read_at' => now()]);

        return response()->json(['data' => [...$notification->only('id', 'kind', 'target_type', 'target_id'), 'patient_public_id' => $notification->patient->public_id, 'patient_name' => $notification->patient->name]]);
    }

    public function device(Request $request): JsonResponse
    {
        $data = $request->validate(['token' => ['required', 'string', 'max:4096'], 'platform' => ['required', 'in:android,ios'], 'locale' => ['required', 'in:ar,en'], 'enabled' => ['required', 'boolean']]);
        $token = $request->user()->currentAccessToken();
        abort_unless($token instanceof PersonalAccessToken, 422);
        $device = PatientDevice::updateOrCreate(['token_hash' => hash('sha256', $data['token'])], [...$data, 'user_id' => $request->user()->id, 'personal_access_token_id' => $token->id]);

        return response()->json(['data' => $device->only('id', 'enabled', 'platform', 'locale')]);
    }

    public function favorites(Request $request): JsonResponse
    {
        return response()->json(['data' => PatientFavorite::where('user_id', $request->user()->id)->with('doctor:id,name,verification_status')->get()->map(fn ($f) => ['doctor_id' => $f->doctor_id, 'name' => $f->doctor->name, 'available' => $f->doctor->verification_status === 'verified'])]);
    }

    public function favorite(Request $request, int $doctor): JsonResponse
    {
        if ($request->isMethod('delete')) {
            PatientFavorite::where('user_id', $request->user()->id)->where('doctor_id', $doctor)->delete();
        } else {
            Doctor::where('verification_status', 'verified')->findOrFail($doctor);
            PatientFavorite::firstOrCreate(['user_id' => $request->user()->id, 'doctor_id' => $doctor]);
        }

        return $this->favorites($request);
    }

    public function reminders(Request $request): JsonResponse
    {
        $patient = $this->patient($request);

        return response()->json(['data' => PatientReminder::where('patient_id', $patient->id)->where('created_by', $request->user()->id)->orderBy('time')->get()->map(fn ($r) => [...$r->only('id', 'title', 'time', 'timezone', 'enabled', 'prescription_id'), 'source' => $r->prescription_id ? 'prescription' : 'patient_entry'])])->header('Cache-Control', 'private, no-store');
    }

    public function reminder(Request $request, string $patient, ?int $reminder = null): JsonResponse
    {
        $patient = $this->patient($request);
        if ($request->isMethod('delete')) {
            PatientReminder::where('patient_id', $patient->id)->where('created_by', $request->user()->id)->findOrFail($reminder)->delete();

            return $this->reminders($request);
        }
        $data = $request->validate(['title' => ['required', 'string', 'max:150'], 'time' => ['required', 'date_format:H:i'], 'timezone' => ['required', Rule::in(\DateTimeZone::listIdentifiers())], 'enabled' => ['required', 'boolean'], 'prescription_id' => ['nullable', 'integer']]);
        if (! empty($data['prescription_id'])) {
            DoctorPrescription::where('status', 'approved')->whereNotNull('approved_at')->whereHas('doctorVisit.doctorAppointment.patient', fn ($q) => $q->where('patient_id', $patient->id))->whereDoesntHave('correction', fn ($q) => $q->whereNotNull('approved_at'))->findOrFail($data['prescription_id']);
        }
        $record = $reminder ? PatientReminder::where('patient_id', $patient->id)->where('created_by', $request->user()->id)->findOrFail($reminder) : new PatientReminder;
        $record->fill([...$data, 'patient_id' => $patient->id, 'created_by' => $request->user()->id])->save();
        $patient->recordAccess('personal_reminder_saved', $request->user()->id, detail: ['reminder_id' => $record->id]);

        return $this->reminders($request);
    }

    public function review(Request $request, string $patient, int $appointment): JsonResponse
    {
        $patient = $this->patient($request);
        $record = DoctorAppointment::whereHas('patient', fn ($q) => $q->where('patient_id', $patient->id))->where('status', 'completed')->whereHas('visit', fn ($q) => $q->whereNotNull('closed_at'))->findOrFail($appointment);
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'comment' => ['nullable', 'string', 'max:1000']]);
        $review = PatientReview::updateOrCreate(['doctor_appointment_id' => $record->id], [...$data, 'patient_id' => $patient->id, 'user_id' => $request->user()->id, 'doctor_id' => $record->doctor_id, 'status' => 'pending', 'reviewed_by' => null, 'review_reason' => null]);
        $patient->recordAccess('review_submitted', $request->user()->id, detail: ['review_id' => $review->id]);

        return response()->json(['data' => $review->only('id', 'rating', 'comment', 'status')]);
    }
}
