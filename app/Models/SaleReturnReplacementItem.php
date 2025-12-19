<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnReplacementItem extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    
    public function transaction()
    {
        return $this->belongsTo(SaleReturnReplacementTransaction::class, 'transaction_id');
    }
    
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
