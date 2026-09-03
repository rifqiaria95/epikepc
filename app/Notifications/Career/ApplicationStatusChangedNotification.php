<?php

namespace App\Notifications\Career;

use App\Models\Career\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $history = $this->application->statusHistories()->latest('created_at')->first();

        return (new MailMessage)
            ->subject('Pembaruan status lamaran — '.$this->application->reference_number)
            ->view('emails.career.status-changed', [
                'application' => $this->application,
                'candidateName' => $this->application->candidate?->full_name,
                'vacancyTitle' => $this->application->vacancy?->title,
                'reference' => $this->application->reference_number,
                'publicStatus' => $this->application->status->publicLabel(),
                'publicMessage' => $history?->public_message,
            ]);
    }
}
