<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected License $license;

    public function __construct(License $license)
    {
        $this->license = $license;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $organization = $this->license->organization;
        
        return (new MailMessage)
            ->subject('IMPORTANT: Your MediBill License Has Expired')
            ->greeting('Hello ' . $notifiable->full_name . '!')
            ->error()
            ->line('Your MediBill license for **' . $organization->name . '** has expired.')
            ->line('**License Key:** ' . substr($this->license->license_key, 0, 12) . '...')
            ->line('**Expired On:** ' . $this->license->expires_at->format('d M Y'))
            ->line('')
            ->line('Your access to MediBill has been restricted. To restore full access, please renew your license immediately.')
            ->action('Renew Now', url('/admin/organization/license'))
            ->line('If you need assistance with renewal, please contact our support team.')
            ->salutation('Best regards, MediBill Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'license_expired',
            'license_id' => $this->license->id,
            'organization_id' => $this->license->organization_id,
            'organization_name' => $this->license->organization->name,
            'expired_at' => $this->license->expires_at->toDateString(),
            'message' => 'Your license has expired',
        ];
    }
}
