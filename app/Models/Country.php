<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Country Entity
 *
 * @property $id
 * @property $name
 * @property $code
 * @property $ddi
 * @property $create_at
 * @property $updated_at
 * @property $deleted_at
 * @property $addresses
 * @package App\Models
 */
class Country extends Model
{
    use SoftDeletes;

    const BRA = 'bra';
    const USA = 'usa';

    public function addresses() {

        return $this->hasMany(Address::class);
    }
}
