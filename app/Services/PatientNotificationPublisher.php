<?php

namespace App\Services;

use App\Jobs\SendPatientPush;
use App\Models\DoctorAppointment;
use App\Models\Patient;
use App\Models\PatientNotification;

class PatientNotificationPublisher
{
    public function publish(Patient $patient, string $kind, string $targetType, int $targetId, string $eventKey): void
    {
        $users = $patient->delegations()->where('status', 'approved')->where('expires_at', '>', now())->pluck('user_id')->push($patient->user_id)->filter()->unique();
        foreach ($users as $userId) {
            $notification = PatientNotification::firstOrCreate(['user_id' => $userId, 'event_key' => $eventKey], ['patient_id' => $patient->id, 'kind' => $kind, 'target_type' => $targetType, 'target_id' => $targetId]);
            if ($notification->wasRecentlyCreated && config('patient.fcm_project') && config('patient.fcm_service_account')) {
                SendPatientPush::dispatch($notification->id)->afterCommit();
            } elseif ($notification->wasRecentlyCreated) {
                $notification->update(['push_state' => 'skipped']);
            }
        }
    }

    public function appointment(DoctorAppointment $appointment): void
    {
        $patient = $appointment->patient?->theraPatient;
        if ($patient) {
            $this->publish($patient, 'appointment_updated', 'appointment', $appointment->id, 'appointment:'.$appointment->id.':'.$appointment->revision);
        }
    }

    public function document(object $document, string $type): void
    {
        $visit = $type === 'visit' ? $document : ($type === 'report' ? $document->order->doctorVisit : $document->doctorVisit);
        $patient = $visit->doctorAppointment->patient?->theraPatient;
        $approved = $type === 'visit' ? $document->closed_at : ($type === 'report' ? $document->reviewed_at : $document->approved_at);
        if ($patient && $approved) {
            $this->publish($patient, 'medical_document', $type, $document->id, $type.':'.$document->id.':'.($document->revision ?? 1).':'.$approved->timestamp);
        }
    }
}
