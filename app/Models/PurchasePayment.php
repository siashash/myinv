<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    protected $table = 'purchase_payments';

    protected $fillable = [
        'purchase_id',
        'supplier_id',
        'supplier_name',
        'supplier_inv_no',
        'invoice_amount',
        'payment_amount',
        'payment_mode',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'invoice_amount' => 'decimal:2',
            'payment_amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function purchase()
    {
        return $this->belongsTo(PurchaseMaster::class, 'purchase_id');
    }
}
