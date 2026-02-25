<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseMaster extends Model
{
    protected $table = 'purchase_master';

    protected $fillable = [
        'entry_date',
        'supplier_id',
        'supplier_name',
        'supplier_inv_no',
        'purchase_date',
        'tot_taxable_amount',
        'tot_gst_amount',
        'invoice_amount',
        'purchase_mode',
    ];

    public $timestamps = false;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'pur_id');
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_id');
    }

    public function latestPayment()
    {
        return $this->hasOne(PurchasePayment::class, 'purchase_id')->latestOfMany('id');
    }
}
