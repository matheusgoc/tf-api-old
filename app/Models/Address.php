<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Address Entity
 *
 * @package App\Models
 */
class Address extends Model
{
    use SoftDeletes;

    const TYPE_AGENT = 'AG';
    const TYPE_BILLING = 'BI';
    const TYPE_SHIPPING = 'SH';

    protected $appends = ['country_name'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCountryNameAttribute()
    {
        return $this->country->name;
    }
}
