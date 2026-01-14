<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SalesMan extends Model
{
    use BelongsToOrganization;

    protected $table = 'sales_men';
    
    protected $fillable = [
        'code',
        'name',
        'email',
        'mobile',
        'telephone',
        'address',
        'city',
        'pin',
        'sales_type',
        'delivery_type',
        'area_mgr_code',
        'area_mgr_name',
        'monthly_target',
        'status',
        'is_deleted',
        'created_date',
        'modified_date'
    ];

    protected $casts = [
        'monthly_target' => 'decimal:2',
        'is_deleted' => 'integer',
        'created_date' => 'datetime',
        'modified_date' => 'datetime'
    ];

    /**
     * Relationship with AreaManager
     */
    public function areaManager()
    {
        return $this->belongsTo(AreaManager::class, 'area_mgr_code', 'id');
    }
}

