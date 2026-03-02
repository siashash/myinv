<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $table = 'sales_returns';

    protected $fillable = [
        'sale_id',
        'customer_id',
        'customer_name',
        'sale_invoice_no',
        'return_no',
        'return_date',
        'total_return_amount',
    ];

    public $timestamps = false;

    public function sale()
    {
        return $this->belongsTo(SaleMaster::class, 'sale_id');
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class, 'sales_return_id');
    }
}
