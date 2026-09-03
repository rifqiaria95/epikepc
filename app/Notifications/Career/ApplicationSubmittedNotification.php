<?php

namespace App\Notifications\Career;

use App\Models\Career\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
        public string $statusPlaintextToken,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('frontend.careers.status', ['token' => $this->statusPlaintextToken]);

        return (new MailMessage)
            ->subject('Lamaran Anda telah diterima — '.$this->application->reference_number)
            ->view('emails.career.application-submitted', [
                'application' => $this->application,
                'candidateName' => $this->application->candidate?->full_name,
                'vacancyTitle' => $this->application->vacancy?->title,
                'reference' => $this->application->reference_number,
                'statusUrl' => $url,
            ]);
    }
}
