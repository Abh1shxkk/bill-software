<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price_monthly',
        'price_yearly',
        'max_users',
        'max_items',
        'max_transactions_per_month',
        'validity_days',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'max_users' => 'integer',
        'max_items' => 'integer',
        'max_transactions_per_month' => 'integer',
        'validity_days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all licenses using this plan
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'plan_id');
    }

    /**
     * Check if a feature is included in this plan
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return isset($features[$feature]) && $features[$feature] === true;
    }

    /**
     * Get formatted monthly price
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return '₹' . number_format($this->price_monthly, 2);
    }

    /**
     * Get formatted yearly price
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        return '₹' . number_format($this->price_yearly, 2);
    }

    /**
     * Get yearly savings percentage
     */
    public function getYearlySavingsPercentAttribute(): float
    {
        if ($this->price_monthly <= 0) {
            return 0;
        }
        
        $monthlyTotal = $this->price_monthly * 12;
        $savings = $monthlyTotal - $this->price_yearly;
        
        return round(($savings / $monthlyTotal) * 100, 1);
    }

    /**
     * Scope to filter active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get trial plan
     */
    public static function trial(): ?self
    {
        return self::where('code', 'trial')->first();
    }

    /**
     * Get plan by code
     */
    public static function byCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }
}
