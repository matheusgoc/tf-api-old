<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Address Entity
 *
 * @property $id
 * @property $user_id
 * @property $user
 * @property $country_id
 * @property $country
 * @property $type
 * @property $zip
 * @property $address
 * @property $number
 * @property $state
 * @property $city
 * @property $create_at
 * @property $updated_at
 * @property $deleted_at
 * @package App\Models
 */
class Address extends Model
{
    use SoftDeletes;

    const TYPE_BILLING = 'B';
    const TYPE_SHIPPING = 'S';

    public function country() {

        return $this->belongsTo(Country::class);
    }

    public function user() {

        return $this->belongsTo(User::class);
    }
}
