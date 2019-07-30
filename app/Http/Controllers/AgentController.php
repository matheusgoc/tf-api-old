<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Throwable;
use App\Models\Agent;
use App\Models\Role;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * Agent Controller
 *
 * @package App\Http\Controllers
 */
class AgentController extends Controller
{
    private $service;
    private $validation = [
        'name' => 'required|alpha_space|min:2|max:100',
        'email_confirmation' => 'required|same:email',
        'country' => 'required|alpha|size:3',
        'zip' => 'required|min:3|max:10',
        'address' => 'required|max:255',
        'state' => 'required|alpha|size:2',
        'city' => 'required|alpha|min:2|max:20',
        'nickname' => 'required|alpha_space|min:2|max:20',
        'phone' => 'digits_between:5,20',
        'birthday' => 'required|date'
    ];

    public function __construct(AgentService $service)
    {
        $this->authorizeResource(Agent::class, 'agent');
        $this->service = $service;
    }

    /**
     * List all agents
     *
     * @throws AuthorizationException
     * @return Response
     */
    public function index()
    {
        $this->authorize('viewAny', Agent::class);

        return response($this->service->getAll());
    }

    /**
     * Create new agent
     *
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function store(Request $request)
    {
        $request->validate(array_merge($this->validation, [
            'password' => 'required|min:4|max:16',
            'password_confirmation' => 'required|same:password',
            'email' => ['bail', 'required', 'email', 'max:200',
                function ($attribute, $value, $fail) {

                    $hasAgent = Agent::where('users.email', $value)
                        ->join('users', 'users.id', '=', 'agents.user_id')
                        ->exists();

                    if ($hasAgent) {
                        $fail(__('validation.unique_agent'));
                    }
                }
            ],
            'role' => ['bail', 'required',
                Rule::in(Role::pluck('id')->toArray()),
                function ($attribute, $role, $fail) {

                    $roleLevel = Role::getLevelByRole($role);
                    $userRoleLevel = auth()->user()->level;

                    if ($roleLevel >= $userRoleLevel && $userRoleLevel != Role::MASTER_LEVEL) {
                        $fail(__('validation.user_level'));
                    }
                }
            ]
        ]));

        $agent = $this->service->create($request->all());

        return response($agent, 201);
    }

    /**
     * Retrieve an agent
     *
     * @param Agent $agent
     * @return Response
     */
    public function show(Agent $agent)
    {
        return response($this->service->get($agent));
    }

    /**
     * Update an agent
     *
     * @param Request $request
     * @param Agent $agent
     * @return Response
     * @throws Throwable
     */
    public function update(Request $request, Agent $agent)
    {
        $request->validate(array_merge($this->validation, [
            'email' => ['required', 'email', 'max:200',
                Rule::unique('users')->ignore($agent->user->id)
            ]
        ]));

        $agent = $this->service->update($request->all(), $agent);

        return response($agent);
    }

    /**
     * Remove an agent
     *
     * @param Agent $agent
     * @return Response
     * @throws Throwable
     */
    public function destroy(Agent $agent)
    {
        $this->service->delete($agent);

        return response('', 204);
    }
}
