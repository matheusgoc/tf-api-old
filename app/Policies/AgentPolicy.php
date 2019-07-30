<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Agent Policy
 *
 * @package App\Policies
 */
class AgentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any agents.
     *
     * @param User $user
     * @return boolean
     */
    public function viewAny(User $user)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can view the agent.
     *
     * @param User $user
     * @param Agent $agent
     * @return boolean
     */
    public function view(User $user, Agent $agent)
    {
        return $agent->user->id === $user->id ||
            (
                $user->level >= Role::ADMIN_LEVEL &&
                (
                    $user->level === Role::MASTER_LEVEL ||
                    $user->level > $agent->user->level
                )
            );
    }

    /**
     * Determine whether the user can create agents.
     *
     * @param User $user
     * @return boolean
     */
    public function create(User $user)
    {
        return $user->level >= Role::ADMIN_LEVEL;
    }

    /**
     * Determine whether the user can update the agent.
     *
     * @param User $user
     * @param Agent $agent
     * @return boolean
     */
    public function update(User $user, Agent $agent)
    {
        return $agent->user->id === $user->id ||
            (
                $user->level >= Role::ADMIN_LEVEL &&
                (
                    $user->level === Role::MASTER_LEVEL ||
                    $user->level > $agent->user->level
                )
            );
    }

    /**
     * Determine whether the user can delete the agent.
     *
     * @param User $user
     * @param Agent $agent
     * @return boolean
     */
    public function delete(User $user, Agent $agent)
    {
        return $agent->user->id <> $user->id &&
            $user->level >= Role::ADMIN_LEVEL &&
            (
                $user->level === Role::MASTER_LEVEL ||
                $user->level > $agent->user->level
            );
    }

    /**
     * Determine whether the user can restore the agent.
     *
     * @param User $user
     * @param Agent $agent
     * @return boolean
     */
    public function restore(User $user, Agent $agent)
    {
        return $user->level >= Role::MASTER_LEVEL;
    }

    /**
     * Determine whether the user can permanently delete the agent.
     *
     * @param User $user
     * @param Agent $agent
     * @return boolean
     */
    public function forceDelete(User $user, Agent $agent)
    {
        return $agent->user->id <> $user->id && $user->level === Role::MASTER_LEVEL;
    }
}
