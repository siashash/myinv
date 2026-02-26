<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = 'purchase_details';

    protected $fillable = [
        'pur_id',
        'product_id',
        'product_name',
        'hsn_code',
        'sales_unit',
        'qty',
        'sale_price',
        'amount',
        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'igst_percent',
        'igst_amount',
        'gst_amount',
        'net_amount',
    ];

    public $timestamps = false;

    public function purchase()
    {
        return $this->belongsTo(PurchaseMaster::class, 'pur_id');
    }

    public function returnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_detail_id');
    }
}
