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

    public $incrementing = false;

    public static function getLevelByRole($role) {
        $level = 0;
        switch ($role) {
            case self::MASTER: $level = self::MASTER_LEVEL; break;
            case self::ADMIN: $level = self::ADMIN_LEVEL; break;
            case self::STAFF: $level = self::STAFF_LEVEL; break;
            case self::CUSTOMER: $level = self::CUSTOMER_LEVEL; break;
        }

        return $level;
    }

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
