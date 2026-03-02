<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $table = 'sales_return_items';

    protected $fillable = [
        'sales_return_id',
        'sale_detail_id',
        'product_id',
        'item_code',
        'product_name',
        'uom',
        'sale_qty',
        'return_qty',
        'rate',
        'amount',
    ];

    public $timestamps = false;

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'sales_return_id');
    }
}
