<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Throwable;

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
        $this->service = $service;
    }

    /**
     * List users
     *
     * @return Response
     */
    public function index()
    {
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

        $customer = $this->service->create($request);

        return response($customer, 201);
    }

    /**
     * Generate a new customer for a given user
     *
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws Throwable
     */
    public function storeByUser(Request $request, User $user)
    {
        $request->validate(array_merge($this->validation, [
            'email' => ['required', 'email', 'max:200',
                Rule::unique('users')->ignore($user->id)
            ]
        ]));

        $customer = $this->service->create($request, $user);

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
     * Update customer
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

        $this->service->update($request, $customer);

        return response($customer);
    }

    /**
     * Remove customer
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
