<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $table = 'purchase_return_items';

    protected $fillable = [
        'purchase_return_id',
        'purchase_detail_id',
        'product_id',
        'product_name',
        'uom',
        'purchase_qty',
        'return_qty',
        'rate',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'purchase_qty' => 'decimal:3',
            'return_qty' => 'decimal:3',
            'rate' => 'decimal:4',
            'amount' => 'decimal:2',
        ];
    }
}
