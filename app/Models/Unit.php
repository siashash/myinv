<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'prim_uom',
        'prim_uom_conv',
        'sec_uom',
        'sec_uom_conv',
        'base_unit',
        'sales_unit',
        'conversion_factor',
    ];

    public $timestamps = false;

    public function getPrimUomAttribute($value): ?string
    {
        return $value ?? $this->attributes['base_unit'] ?? null;
    }

    public function getPrimUomConvAttribute($value): float
    {
        if ($value !== null && (float) $value > 0) {
            return (float) $value;
        }

        return 1.0;
    }

    public function getSecUomAttribute($value): ?string
    {
        return $value ?? $this->attributes['sales_unit'] ?? null;
    }

    public function getSecUomConvAttribute($value): float
    {
        if ($value !== null && (float) $value > 0) {
            return (float) $value;
        }

        $legacy = $this->attributes['conversion_factor'] ?? null;

        if ($legacy !== null && (float) $legacy > 0) {
            return (float) $legacy;
        }

        return 1.0;
    }

    public function getBaseUnitAttribute(): ?string
    {
        return $this->prim_uom;
    }

    public function getSalesUnitAttribute(): ?string
    {
        return $this->sec_uom;
    }

    public function getConversionFactorAttribute(): float
    {
        $primary = (float) ($this->prim_uom_conv ?? 1);
        $secondary = (float) ($this->sec_uom_conv ?? 1);

        if ($primary <= 0 || $secondary <= 0) {
            return 1.0;
        }

        return $secondary / $primary;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
