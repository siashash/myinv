<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';

    protected $fillable = [
        'purchase_id',
        'sale_id',
        'entry_date',
        'product_id',
        'product_name',
        'uom',
        'qty',
        'supplier_id',
        'batch_id',
    ];

    public $timestamps = false;
}
