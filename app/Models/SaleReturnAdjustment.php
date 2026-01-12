<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class SaleReturnAdjustment extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'sale_return_id',
        'sale_transaction_id',
        'adjusted_amount',
    ];

    protected $casts = [
        'adjusted_amount' => 'decimal:2',
    ];

    /**
     * Get the sale return transaction
     */
    public function saleReturn()
    {
        return $this->belongsTo(SaleReturnTransaction::class, 'sale_return_id');
    }

    /**
     * Get the original sale transaction
     */
    public function saleTransaction()
    {
        return $this->belongsTo(SaleTransaction::class, 'sale_transaction_id');
    }
}
