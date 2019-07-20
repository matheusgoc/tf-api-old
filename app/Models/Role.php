<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const MASTER = 'MA';
    const ADMIN = 'AD';
    const STAFF = 'ST';
    const CUSTOMER = 'CU';

    const MASTER_LEVEL = 40;
    const ADMIN_LEVEL = 30;
    const STAFF_LEVEL = 20;
    const CUSTOMER_LEVEL = 10;

    public $timestamps = false;

    /**
     * Retrieve users associate with the role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
