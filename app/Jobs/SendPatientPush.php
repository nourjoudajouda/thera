<?php

namespace App\Jobs;

use App\Models\PatientNotification;
use App\Services\PatientPushSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPatientPush implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public int $tries = 3;

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function __construct(public int $notificationId)
    {
        $this->onQueue('patient-notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(PatientPushSender $sender): void
    {
        $notification = PatientNotification::find($this->notificationId);
        if (! $notification || $notification->push_state === 'sent') {
            return;
        }
        $sender->send($notification);
    }

    public function failed(?\Throwable $error): void
    {
        PatientNotification::whereKey($this->notificationId)->update(['push_state' => 'failed']);
    }
}
