<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnReplacementTransaction extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items()
    {
        return $this->hasMany(SaleReturnReplacementItem::class, 'transaction_id');
    }
    
    public static function getNextTrnNo()
    {
        $last = self::orderBy('id', 'desc')->first();
        return $last ? $last->trn_no + 1 : 1;
    }
}
