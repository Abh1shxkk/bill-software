<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Models\User;
use App\Notifications\LicenseExpiryReminder;
use App\Notifications\LicenseExpiredNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLicenseExpiryReminders extends Command
{
    protected $signature = 'license:send-reminders {--dry-run : Show what would be sent without actually sending}';
    protected $description = 'Send license expiry reminder notifications to organization owners';

    // Days before expiry to send reminders
    protected array $reminderDays = [30, 14, 7, 3, 1];

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Checking for expiring licenses...');
        
        $sentReminders = 0;
        $sentExpired = 0;

        foreach ($this->reminderDays as $days) {
            $targetDate = Carbon::now()->addDays($days)->startOfDay();
            
            $licenses = License::where('is_active', true)
                ->whereDate('expires_at', $targetDate)
                ->with(['organization.users' => function($query) {
                    $query->where('is_organization_owner', true)
                          ->orWhere('role', 'admin');
                }])
                ->get();

            foreach ($licenses as $license) {
                $recipients = $license->organization->users;
                
                foreach ($recipients as $user) {
                    if ($isDryRun) {
                        $this->line("Would send {$days}-day reminder to {$user->email} for {$license->organization->name}");
                    } else {
                        $user->notify(new LicenseExpiryReminder($license, $days));
                        $this->line("<info>Sent</info> {$days}-day reminder to {$user->email}");
                    }
                    $sentReminders++;
                }
            }
        }

        // Check for expired licenses (expired today)
        $expiredLicenses = License::where('is_active', true)
            ->whereDate('expires_at', Carbon::yesterday())
            ->with(['organization.users' => function($query) {
                $query->where('is_organization_owner', true)
                      ->orWhere('role', 'admin');
            }])
            ->get();

        foreach ($expiredLicenses as $license) {
            $recipients = $license->organization->users;
            
            foreach ($recipients as $user) {
                if ($isDryRun) {
                    $this->line("Would send expiry notice to {$user->email} for {$license->organization->name}");
                } else {
                    $user->notify(new LicenseExpiredNotification($license));
                    $this->line("<error>Sent</error> expiry notice to {$user->email}");
                }
                $sentExpired++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  Reminder emails: {$sentReminders}");
        $this->line("  Expiry notices: {$sentExpired}");
        
        if ($isDryRun) {
            $this->warn("This was a dry run. No emails were sent.");
        }

        return Command::SUCCESS;
    }
}
