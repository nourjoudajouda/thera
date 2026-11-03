<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class PatientAccountCreated extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(#[\SensitiveParameter] public string $initialPassword, public string $patientReference)
    {
        $this->afterCommit();
        $this->onQueue('patient-onboarding');
    }

    /** @return list<string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    /** @return list<int> */
    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function shouldSend(User $notifiable, string $channel): bool
    {
        return Hash::check($this->initialPassword, $notifiable->password);
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->mailer('smtp')
            ->subject('بيانات حسابك على شبكة THERA')
            ->greeting('مرحبًا '.$notifiable->name)
            ->line('تم إنشاء حساب خاص بك على شبكة ثيرا بواسطة الطبيب.')
            ->line('اسم المستخدم (البريد الإلكتروني): '.$notifiable->email)
            ->line('كلمة المرور الأولية: '.$this->initialPassword)
            ->line('رقم ملف ثيرا: '.$this->patientReference)
            ->line('احتفظ ببيانات الدخول بسرية ولا تشارك كلمة المرور مع أي شخص.')
            ->line('إنشاء الحساب لا يعني توثيق رقم الهاتف أو مشاركة سجلك الطبي مع أطباء آخرين.')
            ->salutation('فريق THERA');
    }
}
