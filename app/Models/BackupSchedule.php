<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupSchedule extends Model
{
    protected $fillable = [
        'frequency',
        'time',
        'day_of_week',
        'day_of_month',
        'compress',
        'is_active',
        'retention_days',
        'last_run_at',
        'next_run_at',
        'created_by',
    ];

    protected $casts = [
        'compress' => 'boolean',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Calculate the next run time based on frequency
     */
    public function calculateNextRun(): void
    {
        $now = now();
        $time = explode(':', $this->time);
        $hour = (int) $time[0];
        $minute = (int) $time[1];

        switch ($this->frequency) {
            case 'daily':
                $next = $now->copy()->setTime($hour, $minute);
                if ($next->lte($now)) {
                    $next->addDay();
                }
                break;

            case 'weekly':
                $next = $now->copy()->setTime($hour, $minute);
                $daysUntil = ($this->day_of_week - $now->dayOfWeek + 7) % 7;
                if ($daysUntil === 0 && $next->lte($now)) {
                    $daysUntil = 7;
                }
                $next->addDays($daysUntil);
                break;

            case 'monthly':
                $next = $now->copy()->setTime($hour, $minute)->setDay(min($this->day_of_month, $now->daysInMonth));
                if ($next->lte($now)) {
                    $next->addMonth();
                    $next->setDay(min($this->day_of_month, $next->daysInMonth));
                }
                break;
        }

        $this->next_run_at = $next;
        $this->save();
    }

    /**
     * Get frequency label
     */
    public function getFrequencyLabelAttribute(): string
    {
        $labels = [
            'daily' => 'Daily at ' . $this->time,
            'weekly' => 'Weekly on ' . $this->getDayName() . ' at ' . $this->time,
            'monthly' => 'Monthly on day ' . $this->day_of_month . ' at ' . $this->time,
        ];

        return $labels[$this->frequency] ?? $this->frequency;
    }

    protected function getDayName(): string
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$this->day_of_week] ?? '';
    }
}
