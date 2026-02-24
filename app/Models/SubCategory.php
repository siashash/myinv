<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubCategory extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_name',
        'status',
    ];

    public $timestamps = false;

    public function setSubCategoryNameAttribute($value): void
    {
        $value = trim((string) $value);
        $this->attributes['sub_category_name'] = $value === '' ? $value : Str::ucfirst(Str::lower($value));
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
