<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Customer Entity
 *
 * @property $id
 * @property $user_id
 * @property User $user
 * @property Address $address
 * @property $name
 * @property $email
 * @property $document
 * @property $birthday
 * @property $gender
 * @property $phone
 * @property $news
 * @property $terms
 * @property $create_at
 * @property $updated_at
 * @property $deleted_at
 * @package App\Models
 */
class Customer extends Model
{
    use SoftDeletes;

//    protected $with = ['address'];

//    protected $appends = ['name', 'email'];

    public function user() {

        return $this->belongsTo(User::class);
    }

    /**
     * @return Address
     */
    public function address() {

        return $this->hasOne(Address::class, 'user_id', 'user_id')
            ->where('type', Address::TYPE_BILLING)->limit(1);
    }

    public function getNameAttribute()
    {
        return $this->user->name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }
}
