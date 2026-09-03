<?php

namespace App\Notifications\Career;

use App\Models\Career\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
        public string $plaintextToken,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('frontend.careers.verify', ['token' => $this->plaintextToken]);

        return (new MailMessage)
            ->subject('Verifikasi lamaran Anda di EPIKEPC')
            ->view('emails.career.verify-application', [
                'application' => $this->application,
                'candidateName' => $this->application->candidate?->full_name,
                'vacancyTitle' => $this->application->vacancy?->title,
                'url' => $url,
                'hours' => (int) config('career.tokens.verification_ttl_hours', 48),
            ]);
    }
}
