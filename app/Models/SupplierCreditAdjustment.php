<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierCreditAdjustment extends Model
{
    protected $table = 'supplier_credit_adjustments';

    protected $fillable = [
        'supplier_credit_note_id',
        'supplier_id',
        'purchase_id',
        'adjusted_amount',
        'adjusted_date',
    ];

    protected function casts(): array
    {
        return [
            'adjusted_amount' => 'decimal:2',
            'adjusted_date' => 'date',
        ];
    }
}
