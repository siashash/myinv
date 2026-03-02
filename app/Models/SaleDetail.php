<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $table = 'sales_details';

    protected $fillable = [
        'sale_id',
        'product_id',
        'item_code',
        'product_name',
        'uom',
        'unit_name',
        'qty',
        'rate',
        'amount',
        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'igst_percent',
        'igst_amount',
        'gst_amount',
        'net_amount',
        'total',
    ];

    public $timestamps = false;

    public function sale()
    {
        return $this->belongsTo(SaleMaster::class, 'sale_id');
    }

    public function returnItems()
    {
        return $this->hasMany(SalesReturnItem::class, 'sale_detail_id');
    }
}
