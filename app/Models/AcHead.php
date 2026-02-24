<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AcHead extends Model
{
    protected $table = 'ac_head';

    protected $fillable = [
        'ac_headname',
        'mode',
    ];

    public $timestamps = false;

    public function setAcHeadnameAttribute($value): void
    {
        $value = trim((string) $value);
        $this->attributes['ac_headname'] = $value === '' ? $value : Str::ucfirst(Str::lower($value));
    }
}
