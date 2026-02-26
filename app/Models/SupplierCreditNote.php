<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierCreditNote extends Model
{
    protected $table = 'supplier_credit_notes';

    protected $fillable = [
        'purchase_return_id',
        'supplier_id',
        'purchase_id',
        'credit_note_no',
        'credit_amount',
        'remaining_amount',
        'note_date',
    ];

    protected function casts(): array
    {
        return [
            'credit_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'note_date' => 'date',
        ];
    }

    public function adjustments()
    {
        return $this->hasMany(SupplierCreditAdjustment::class, 'supplier_credit_note_id');
    }
}
