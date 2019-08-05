<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Models\Image;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagePolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any product images.
     *
     * @param User $user
     * @return boolean
     */
    public function viewAny(User $user)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can view the product image.
     *
     * @param User  $user
     * @param Image $image
     * @return boolean
     */
    public function view(User $user, Image $image)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can create product images.
     *
     * @param User $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can update the product image.
     *
     * @param User $user
     * @param Image $image
     * @return mixed
     */
    public function update(User $user, Image $image)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can delete the product image.
     *
     * @param User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can restore the product image.
     *
     * @param User $user
     * @param Image $image
     * @return mixed
     */
    public function restore(User $user, Image $image)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can permanently delete the product image.
     *
     * @param User $user
     * @param Image $image
     * @return mixed
     */
    public function forceDelete(User $user, Image $image)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }
}
