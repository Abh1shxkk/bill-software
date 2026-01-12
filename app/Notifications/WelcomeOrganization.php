<?php

namespace App\Notifications;

use App\Models\Organization;
use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeOrganization extends Notification implements ShouldQueue
{
    use Queueable;

    protected Organization $organization;
    protected License $license;

    public function __construct(Organization $organization, License $license)
    {
        $this->organization = $organization;
        $this->license = $license;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to MediBill - Your Account is Ready!')
            ->greeting('Welcome ' . $notifiable->full_name . '!')
            ->line('Thank you for registering with **MediBill**. Your organization account has been successfully created.')
            ->line('')
            ->line('**Organization Details:**')
            ->line('• Name: ' . $this->organization->name)
            ->line('• Code: ' . $this->organization->code)
            ->line('')
            ->line('**License Information:**')
            ->line('• Plan: ' . ucfirst($this->license->plan_type))
            ->line('• License Key: ' . $this->license->license_key)
            ->line('• Valid Until: ' . $this->license->expires_at->format('d M Y'))
            ->line('')
            ->line('Keep your license key safe. You may need it for future reference.')
            ->action('Go to Dashboard', url('/admin/dashboard'))
            ->line('If you have any questions, our support team is here to help!')
            ->salutation('Best regards, MediBill Team');
    }
}
