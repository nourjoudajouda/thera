<?php

namespace App\Http\Controllers\Doctor\Appointments;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAppointment;
use App\Models\DoctorFacility;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class DoctorQueueController extends Controller
{
    public function store(Request $request, DoctorAppointment $doctorAppointment): JsonResponse|RedirectResponse
    {
        Gate::forUser($request->user('doctor'))->authorize('update', $doctorAppointment);
        $request->validate(['revision' => ['required', 'integer', 'min:1']]);
        DB::transaction(function () use ($request, $doctorAppointment): void {
            Doctor::query()->lockForUpdate()->findOrFail($request->user('doctor')->id);
            $doctorAppointment->refresh()->load(['queueEntry', 'doctorFacility.facility'])->loadSum('payments as paid_minor', 'amount_minor');
            if (! in_array($doctorAppointment->queueState(), ['ready', 'called'], true) || $doctorAppointment->doctorFacility->membership_status !== DoctorFacility::STATUS_ACTIVE || ! $doctorAppointment->doctorFacility->facility->is_active) {
                throw ValidationException::withMessages(['revision' => 'الاستدعاء يتطلب موعدًا مؤكدًا اليوم، وحضورًا مسجلًا، وتسديد المتبقي، وعضوية ومنشأة نشطتين.']);
            }
            if ($doctorAppointment->queueEntry) {
                return;
            }
            if ($doctorAppointment->revision !== $request->integer('revision')) {
                throw ValidationException::withMessages(['revision' => 'تغيرت بيانات الموعد؛ حدّث العرض قبل الاستدعاء.']);
            }
            $entry = $doctorAppointment->queueEntry()->create(['doctor_id' => $request->user('doctor')->id, 'called_at' => CarbonImmutable::now('UTC')]);
            $doctorAppointment->update(['revision' => $doctorAppointment->revision + 1]);
            $doctorAppointment->events()->create(['doctor_id' => $request->user('doctor')->id, 'action' => 'patient_called', 'after' => ['queue_entry_id' => $entry->id, 'called_at' => $entry->called_at->toIso8601String(), 'revision' => $doctorAppointment->revision]]);
        }, 3);
        $message = 'سُجل استدعاء المريض. لم تبدأ زيارة طبية تلقائيًا.';

        return $request->expectsJson() ? response()->json(['message' => $message]) : back()->with('status', $message);
    }
}
