<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'customer_name',
        'email_id',
        'phone',
        'address',
        'gst_no',
        'status',
    ];

    public $timestamps = false;

    public function setCustomerNameAttribute($value): void
    {
        $this->attributes['customer_name'] = $this->toSentenceCase($value);
    }

    public function setAddressAttribute($value): void
    {
        $this->attributes['address'] = $this->toSentenceCase($value);
    }

    public function setGstNoAttribute($value): void
    {
        $this->attributes['gst_no'] = $this->toSentenceCase($value);
    }

    public function setEmailIdAttribute($value): void
    {
        $value = trim((string) $value);
        $this->attributes['email_id'] = $value === '' ? null : Str::lower($value);
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = trim((string) $value);
    }

    private function toSentenceCase($value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return Str::ucfirst(Str::lower($value));
    }
}
