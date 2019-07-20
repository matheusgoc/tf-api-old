<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Retrieve user's roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function isMaster()
    {
        return $this->checkRole(Role::MASTER);
    }

    public function isAdmin()
    {
        return $this->checkRole(Role::ADMIN);
    }

    public function isStaff()
    {
        return $this->checkRole(Role::STAFF);
    }

    public function isCustomer()
    {
        return $this->checkRole(Role::CUSTOMER);
    }

    private function checkRole($roleId)
    {
        return $this->roles()->where('roles.id', $roleId)->exists();
    }

    public function getLevelAttribute()
    {
        return $this->roles()->max('level');
    }
}
