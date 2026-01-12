<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'pin_code',
        'gst_no',
        'pan_no',
        'dl_no',
        'dl_no_1',
        'food_license',
        'logo_path',
        'timezone',
        'currency',
        'date_format',
        'status',
        'notes',
        // Branding fields
        'primary_color',
        'secondary_color',
        'accent_color',
        'sidebar_color',
        'header_color',
        'favicon_path',
        'login_background_path',
        'app_name',
        'tagline',
        'footer_text',
        'invoice_header_html',
        'invoice_footer_html',
        'invoice_terms',
        'custom_css',
        'show_powered_by',
        'custom_domain_enabled',
        'custom_domain',
        // Auto backup settings
        'auto_backup_enabled',
        'auto_backup_tables',
    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all users belonging to this organization
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'organization_id');
    }

    /**
     * Get the organization owner/admin
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'organization_id')
            ->where('is_organization_owner', true);
    }

    /**
     * Get all licenses for this organization
     */
    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'organization_id');
    }

    /**
     * Get the currently active license
     */
    public function activeLicense(): HasOne
    {
        return $this->hasOne(License::class, 'organization_id')
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->orderBy('expires_at', 'desc');
    }

    /**
     * Check if organization has an active license
     */
    public function hasActiveLicense(): bool
    {
        return $this->activeLicense()->exists();
    }

    /**
     * Check if organization is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->hasActiveLicense();
    }

    /**
     * Get customers belonging to this organization
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'organization_id');
    }

    /**
     * Get suppliers belonging to this organization
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class, 'organization_id');
    }

    /**
     * Get items belonging to this organization
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'organization_id');
    }

    /**
     * Get sale transactions belonging to this organization
     */
    public function saleTransactions(): HasMany
    {
        return $this->hasMany(SaleTransaction::class, 'organization_id');
    }

    /**
     * Get purchase transactions belonging to this organization
     */
    public function purchaseTransactions(): HasMany
    {
        return $this->hasMany(PurchaseTransaction::class, 'organization_id');
    }

    /**
     * Generate a unique organization code
     */
    public static function generateCode(string $name): string
    {
        // Create code from name initials
        $words = explode(' ', strtoupper(preg_replace('/[^a-zA-Z\s]/', '', $name)));
        $code = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $code .= substr($word, 0, 1);
            }
        }
        
        // Ensure minimum 3 characters
        $code = str_pad($code, 3, 'X');
        $code = substr($code, 0, 5);
        
        // Add random suffix for uniqueness
        $code .= strtoupper(substr(uniqid(), -4));
        
        // Check if exists and regenerate if needed
        while (self::where('code', $code)->exists()) {
            $code = substr($code, 0, -4) . strtoupper(substr(uniqid(), -4));
        }
        
        return $code;
    }

    /**
     * Scope to filter active organizations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }


    /**
     * Scope to filter suspended organizations
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Get all auto backup logs for this organization
     */
    public function autoBackupLogs(): HasMany
    {
        return $this->hasMany(AutoBackupLog::class, 'organization_id');
    }

    /**
     * Check if auto backup is enabled for this organization
     */
    public function isAutoBackupEnabled(): bool
    {
        return $this->auto_backup_enabled ?? true;
    }

    /**
     * Get the latest backup for each day of the week
     */
    public function getWeeklyBackupStatus(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $status = [];
        
        foreach ($days as $day) {
            $backup = $this->autoBackupLogs()
                ->where('day_of_week', $day)
                ->where('status', 'success')
                ->orderBy('backup_date', 'desc')
                ->first();
            
            $status[$day] = $backup ? [
                'exists' => true,
                'date' => $backup->backup_date->format('Y-m-d'),
                'size' => $backup->backup_size,
                'size_formatted' => $backup->formatted_size,
                'filename' => $backup->backup_filename,
            ] : [
                'exists' => false,
            ];
        }
        
        return $status;
    }
}
