<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotkey extends Model
{
    use HasFactory;

    protected $fillable = [
        'key_combination',
        'module_name',
        'route_name',
        'category',
        'scope',
        'description',
        'is_active',
        'is_system',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    // Categories
    const CATEGORY_MASTERS = 'masters';
    const CATEGORY_TRANSACTIONS = 'transactions';
    const CATEGORY_RECEIPTS = 'receipts';
    const CATEGORY_NOTES = 'notes';
    const CATEGORY_STOCK = 'stock';
    const CATEGORY_LEDGERS = 'ledgers';
    const CATEGORY_MANAGERS = 'managers';
    const CATEGORY_UTILITIES = 'utilities';
    const CATEGORY_INDEX = 'index'; // For blade-specific shortcuts

    // Scopes
    const SCOPE_GLOBAL = 'global';
    const SCOPE_INDEX = 'index';

    /**
     * Get all categories
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_MASTERS => 'Masters',
            self::CATEGORY_TRANSACTIONS => 'Transactions',
            self::CATEGORY_RECEIPTS => 'Receipts & Payments',
            self::CATEGORY_NOTES => 'Notes & Vouchers',
            self::CATEGORY_STOCK => 'Stock & Transfer',
            self::CATEGORY_LEDGERS => 'Ledgers',
            self::CATEGORY_MANAGERS => 'Managers',
            self::CATEGORY_UTILITIES => 'Utilities',
            self::CATEGORY_INDEX => 'Index Page Shortcuts',
        ];
    }

    /**
     * Get all scopes
     */
    public static function getScopes(): array
    {
        return [
            self::SCOPE_GLOBAL => 'Global (Navigation)',
            self::SCOPE_INDEX => 'Index Page (Blade-specific)',
        ];
    }

    /**
     * Check if key combination is already in use
     */
    public static function isKeyInUse(string $keyCombination, ?int $excludeId = null): bool
    {
        $query = self::where('key_combination', strtolower($keyCombination))
            ->where('is_active', true);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Get active hotkeys by category
     */
    public static function getActiveByCategory(?string $category = null)
    {
        $query = self::where('is_active', true);
        
        if ($category) {
            $query->where('category', $category);
        }
        
        return $query->orderBy('category')->orderBy('module_name')->get();
    }

    /**
     * Get active global hotkeys for navigation
     */
    public static function getActiveGlobalHotkeys()
    {
        return self::where('is_active', true)
            ->where('scope', self::SCOPE_GLOBAL)
            ->get();
    }

    /**
     * Get active index hotkeys
     */
    public static function getActiveIndexHotkeys()
    {
        return self::where('is_active', true)
            ->where('scope', self::SCOPE_INDEX)
            ->get();
    }

    /**
     * Format key combination for display
     */
    public function getFormattedKeyAttribute(): string
    {
        $parts = explode('+', $this->key_combination);
        return implode(' + ', array_map('ucfirst', $parts));
    }
}
