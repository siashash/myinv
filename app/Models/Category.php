<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'category_name',
        'status',
    ];

    public $timestamps = false;

    public function setCategoryNameAttribute($value): void
    {
        $value = trim((string) $value);
        $this->attributes['category_name'] = $value === '' ? $value : Str::ucfirst(Str::lower($value));
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
