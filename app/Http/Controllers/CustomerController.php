<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Customer Controller
 *
 * @package App\Http\Controllers
 */
class CustomerController extends Controller
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
        'phone' => 'digits_between:5,20',
        'birthday' => 'required|date',
        'gender' => 'required|alpha|in:M,F',
        'news' => 'required|boolean',
        'terms' => 'required|accepted'
    ];

    public function __construct(CustomerService $service)
    {
        $this->middleware('auth:api', ['except' => ['store']]);
        $this->authorizeResource(Customer::class, 'customer');
        $this->service = $service;
    }

    /**
     * List users
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function index()
    {
        $this->authorize('viewAny', Customer::class);

        $customers = $this->service->getAll();

        return response($customers);
    }

    /**
     * Generate new customer
     *
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function store(Request $request)
    {
        $request->validate(array_merge($this->validation, [
            'email' => 'required|email|max:200|unique:users',
            'password' => 'required|min:4|max:16',
            'password_confirmation' => 'required|same:password',
        ]));

        $customer = $this->service->create($request->all());

        return response($customer, 201);
    }

    /**
     * Generate new customer for a given user
     *
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws Throwable
     */
    public function storeByUser(Request $request, User $user)
    {
        $this->authorize('createByUser', [Customer::class, $user->id]);

        $request->validate(array_merge($this->validation, [
            'email' => ['required', 'email', 'max:200',
                Rule::unique('users')->ignore($user->id)
            ]
        ]));

        $customer = $this->service->create($request->all(), $user);

        return response($customer, 201);
    }

    /**
     * Retrieve a customer
     *
     * @param  Customer  $customer
     * @return Response
     */
    public function show(Customer $customer)
    {
        return response($this->service->get($customer));
    }

    /**
     * Update a customer
     *
     * @param  Request  $request
     * @param  Customer  $customer
     * @return Response
     * @throws Throwable
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate(array_merge($this->validation, [
            'email' => ['required', 'email', 'max:200',
                Rule::unique('users')->ignore($customer->user->id)
            ]
        ]));

        $this->service->update($request->all(), $customer);

        return response($customer);
    }

    /**
     * Remove a customer
     *
     * @param Customer $customer
     * @return Response
     * @throws Throwable
     */
    public function destroy(Customer $customer)
    {
        $this->service->delete($customer);

        return response(null, 204);
    }
}
