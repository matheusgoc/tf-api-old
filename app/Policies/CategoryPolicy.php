<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any categories.
     *
     * @param User $user
     * @return boolean
     */
    public function viewAny(User $user)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can view the category.
     *
     * @param User $user
     * @param Category $category
     * @return mixed
     */
    public function view(User $user, Category $category)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can create categories.
     *
     * @param User $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can update the category.
     *
     * @param User $user
     * @param Category $category
     * @return boolean
     */
    public function update(User $user, Category $category)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can delete the category.
     *
     * @param User  $user
     * @param Category  $category
     * @return mixed
     */
    public function delete(User $user, Category $category)
    {
        return ($category->products()->count() === 0 && $user->level >= Role::ADMIN_LEVEL) ||
            $user->level >= Role::MASTER_LEVEL;
    }

    /**
     * Determine whether the user can restore the category.
     *
     * @param User  $user
     * @param Category  $category
     * @return mixed
     */
    public function restore(User $user, Category $category)
    {
        return $user->level >= Role::MASTER_LEVEL;
    }

    /**
     * Determine whether the user can permanently delete the category.
     *
     * @param User $user
     * @param Category $category
     * @return boolean
     */
    public function forceDelete(User $user, Category $category)
    {
        return $user->level >= Role::MASTER_LEVEL;
    }
}
