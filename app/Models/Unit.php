<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'base_unit',
        'sales_unit',
        'conversion_factor',
    ];

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
