<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Customer Policy
 *
 * @package App\Policies
 */
class CustomerPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any customers.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can view the customer.
     *
     * @param User $user
     * @param Customer $customer
     * @return mixed
     */
    public function view(User $user, Customer $customer)
    {
        return $user->id === $customer->user_id || $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can create customers.
     *
     * @param User|null $user
     * @return bool
     */
    public function create(?User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create customers by given user ID.
     *
     * @param User $user
     * @param $userId
     * @return mixed
     */
    public function createByUser(User $user, $userId)
    {
        return $user->id === $userId || $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can update the customer.
     *
     * @param User $user
     * @param Customer $customer
     * @return mixed
     */
    public function update(User $user, Customer $customer)
    {
        return $user->id === $customer->user_id || $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can delete the customer.
     *
     * @param  User  $user
     * @param  Customer  $customer
     * @return mixed
     */
    public function delete(User $user, Customer $customer)
    {
        return $user->id === $customer->user_id || $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can restore the customer.
     *
     * @param User $user
     * @param Customer $customer
     * @return mixed
     */
    public function restore(User $user, Customer $customer)
    {
        return $user->level >= Role::MASTER_LEVEL;
    }

    /**
     * Determine whether the user can permanently delete the customer.
     *
     * @param User $user
     * @param Customer $customer
     * @return mixed
     */
    public function forceDelete(User $user, Customer $customer)
    {
        return $user->level >= Role::MASTER_LEVEL;
    }
}
