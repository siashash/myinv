<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $table = 'purchase_returns';

    protected $fillable = [
        'purchase_id',
        'supplier_id',
        'supplier_name',
        'supplier_inv_no',
        'credit_note_no',
        'return_date',
        'total_credit_amount',
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
            'total_credit_amount' => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id');
    }
}
