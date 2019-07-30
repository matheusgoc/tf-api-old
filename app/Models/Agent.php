<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Agent Entity
 *
 * @package App\Models
 */
class Agent extends Model
{
    use SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->hasOne(Address::class, 'user_id', 'user_id')
            ->where('type', Address::TYPE_AGENT)->limit(1);
    }

    public function getRolesAttribute()
    {
        return $this->user->roles;
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
