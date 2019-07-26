<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Role Entity
 *
 * @property $id
 * @property $name
 * @property $description
 * @property $level
 * @property $create_at
 * @property $updated_at
 * @property $deleted_at
 * @package App\Models
 */
class Role extends Model
{
    use SoftDeletes;

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
