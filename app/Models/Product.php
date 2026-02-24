<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'product_name',
        'product_code',
        'hsn_code',
        'purchase_price',
        'sales_price_bu',
        'sales_price_su',
        'sale_price',
        'uom',
        'sales_uom',
        'unit_id',
        'base_unit_id',
        'sale_unit_id',
        'conversion_factor',
        'discount_amount',
        'final_price',
        'opening_stock',
        'gst_percent',
        'cgst_percent',
        'sgst_percent',
        'igst_percent',
        'status',
    ];

    public $timestamps = false;

    public function setProductNameAttribute($value): void
    {
        $value = trim((string) $value);
        $this->attributes['product_name'] = $value === '' ? $value : Str::ucfirst(Str::lower($value));
    }

    public function setProductCodeAttribute($value): void
    {
        $this->attributes['product_code'] = strtoupper(trim((string) $value));
    }

    public function setHsnCodeAttribute($value): void
    {
        $this->attributes['hsn_code'] = strtoupper(trim((string) $value));
    }

    public function setUomAttribute($value): void
    {
        $value = trim((string) $value);
        $this->attributes['uom'] = $value === '' ? $value : Str::upper($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function saleUnit()
    {
        return $this->belongsTo(Unit::class, 'sale_unit_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
