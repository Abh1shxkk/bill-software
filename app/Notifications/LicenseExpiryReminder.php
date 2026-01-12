<?php

namespace App\Notifications;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LicenseExpiryReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected License $license;
    protected int $daysRemaining;

    public function __construct(License $license, int $daysRemaining)
    {
        $this->license = $license;
        $this->daysRemaining = $daysRemaining;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $organization = $this->license->organization;
        $urgency = $this->daysRemaining <= 3 ? 'URGENT: ' : '';
        
        return (new MailMessage)
            ->subject($urgency . 'MediBill License Expiring in ' . $this->daysRemaining . ' Days')
            ->greeting('Hello ' . $notifiable->full_name . '!')
            ->line('Your MediBill license for **' . $organization->name . '** is expiring soon.')
            ->line('**License Key:** ' . substr($this->license->license_key, 0, 12) . '...')
            ->line('**Plan:** ' . ucfirst($this->license->plan_type))
            ->line('**Expiry Date:** ' . $this->license->expires_at->format('d M Y'))
            ->line('**Days Remaining:** ' . $this->daysRemaining)
            ->line('')
            ->line('To avoid any interruption in service, please renew your license before the expiry date.')
            ->action('Renew License', url('/admin/organization/license'))
            ->line('If you have any questions or need assistance, please contact our support team.')
            ->salutation('Best regards, MediBill Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'license_expiry_reminder',
            'license_id' => $this->license->id,
            'organization_id' => $this->license->organization_id,
            'organization_name' => $this->license->organization->name,
            'days_remaining' => $this->daysRemaining,
            'expires_at' => $this->license->expires_at->toDateString(),
            'message' => 'Your license expires in ' . $this->daysRemaining . ' days',
        ];
    }
}
