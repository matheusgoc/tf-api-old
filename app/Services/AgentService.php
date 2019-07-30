<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Agent;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;
use \Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Agent Service
 *
 * @package App\Services
 */
class AgentService
{
    /**
     * Retrieve a list of agents
     *
     * @return LengthAwarePaginator
     */
    public function getAll()
    {
        $userLevel = auth()->user()->level;

        $levelSql = DB::table('roles')
            ->select('user_id', DB::raw('max(roles.level) as level'))
            ->join('role_user', 'roles.id', '=', 'role_user.role_id')
            ->groupBy(['user_id'])
            ->toSql();

        $query = DB::table('agents')
            ->select([
                'agents.id',
                'agents.user_id',
                'users.name',
                'users.email',
                'agents.nickname',
                'agents.phone',
                'roles.level'
            ])
            ->join('users', 'agents.user_id', '=', 'users.id')
            ->join(DB::raw("($levelSql) as roles"), function(JoinClause $join) {
                $join->on('roles.user_id', '=', 'users.id');
            })
            ->whereNull('agents.deleted_at');

        // case is the user does not have the master authority
        if ($userLevel < Role::MASTER_LEVEL) {

            $query->where('roles.level', '<', $userLevel);
        }

        return $query->paginate();
    }

    /**
     * Retrieve an agent
     *
     * @param Agent $agent
     * @return Agent
     */
    public function get(Agent $agent)
    {
        return $agent
            ->load('address')
            ->append(['name', 'email', 'roles']);
    }

    /**
     * Create new agent
     *
     * @param array $data
     * @return Agent
     * @throws Throwable
     */
    public function create(array $data)
    {
        $agent = new Agent();
        DB::transaction(function () use ($data, $agent) {

            $user = User::where('email', $data['email'])->first();
            if (!$user) {

                $user = new User();
                $user->password = bcrypt($data['password']);
            }
            if ($user->email !== $data['email']) {

                $user->email = $data['email'];
                $user->checked_at = null;
            }
            $user->name = $data['name'];
            $user->save();

            $user->roles()->syncWithoutDetaching([$data['role']]);

            $address = ($user->address)?: new Address();
            $address->user_id = $user->id;
            $address->country_id = $data['country'];
            $address->type = Address::TYPE_AGENT;
            $address->zip = $data['zip'];
            $address->address = $data['address'];
            $address->number = $data['number'];
            $address->state = $data['state'];
            $address->city = $data['city'];
            $address->save();

            $agent->user_id = $user->id;
            $agent->nickname = $data['nickname'];
            $agent->phone = $data['phone'];
            $agent->birthday = $data['birthday'];
            $agent->save();
        });

        return $agent
            ->fresh()
            ->load('address')
            ->append(['name', 'email', 'roles']);
    }

    /**
     * Update an agent
     *
     * @param array $data
     * @param Agent $agent
     * @return Agent|null
     * @throws Throwable
     */
    public function update(array $data, Agent $agent)
    {
        DB::transaction(function () use ($data, $agent){

            $agent->user->name = $data['name'];
            if ($agent->user->email !== $data['email']) {

                $agent->user->email = $data['email'];
                $agent->user->checked_at = null;
            }
            $agent->user->save();

            $agent->address->country_id = $data['country'];
            $agent->address->zip = $data['zip'];
            $agent->address->address = $data['address'];
            $agent->address->number = $data['number'];
            $agent->address->state = $data['state'];
            $agent->address->city = $data['city'];
            $agent->address->save();

            $agent->phone = $data['phone'];
            $agent->birthday = $data['birthday'];
            $agent->nickname = $data['nickname'];
            $agent->save();
        });

        return $agent
            ->fresh()
            ->load('address')
            ->append(['name', 'email', 'roles']);
    }

    /**
     * Delete an agent
     *
     * @param Agent $agent
     * @throws Throwable
     */
    public function delete(Agent $agent)
    {
        DB::transaction(function () use ($agent) {

            $agent->address->delete();
            $agent->user->roles()->detach($agent->user->roles->where('id', '<>', Role::CUSTOMER)->pluck('id')->toArray());
            $agent->delete();
        });
    }
}
