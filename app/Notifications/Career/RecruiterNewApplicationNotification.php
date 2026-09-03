<?php

namespace App\Notifications\Career;

use App\Models\Career\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecruiterNewApplicationNotification extends Notification implements ShouldQueue
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
        $url = route('career.applications.show', $this->application->id);

        return (new MailMessage)
            ->subject('Lamaran baru terverifikasi: '.$this->application->vacancy?->title)
            ->view('emails.career.recruiter-new-application', [
                'application' => $this->application,
                'vacancyTitle' => $this->application->vacancy?->title,
                'reference' => $this->application->reference_number,
                'cmsUrl' => $url,
            ]);
    }
}
