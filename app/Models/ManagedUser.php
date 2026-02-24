<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagedUser extends Model
{
    protected $table = 'user';

    protected $fillable = [
        'name',
        'password',
        'role_id',
    ];

    public $timestamps = false;

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
