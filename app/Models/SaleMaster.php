<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleMaster extends Model
{
    protected $table = 'sales_master';

    protected $fillable = [
        'sale_date',
        'invoice_no',
        'customer_id',
        'customer_name',
        'sale_mode',
        'discount_amount',
        'total_amount',
    ];

    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class, 'sale_id');
    }

    public function stockRows()
    {
        return $this->hasMany(Stock::class, 'sale_id');
    }
}
