<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Country Entity
 *
 * @package App\Models
 */
class Country extends Model
{
    use SoftDeletes;

    const BRA = 'bra';
    const USA = 'usa';

    public $incrementing = false;

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
