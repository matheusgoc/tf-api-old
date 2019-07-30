<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Throwable;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Customer Service
 *
 * @package App\Services
 */
class CustomerService
{
    /**
     * List all customers
     *
     * @return LengthAwarePaginator
     */
    public function getAll()
    {
        return DB::table('customers')
            ->select([
                'customers.id',
                'user_id',
                'users.name',
                'users.email',
                'phone',
                'document',
                'gender'
            ])
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->whereNull('customers.deleted_at')
            ->paginate();
    }

    /**
     * Retrieve a customer
     *
     * @param Customer $customer
     * @return Customer
     */
    public function get(Customer $customer)
    {
        return $customer
            ->load('address')
            ->append(['name', 'email']);
    }

    /**
     * Create new customer
     *
     * @param array $data
     * @param User|null $user
     * @return Customer
     * @throws Throwable
     */
    public function create(array $data, User $user = null)
    {
        $customer = ($user && $user->customer)? $user->customer : new Customer();
        DB::transaction(function () use ($data, $customer, $user) {

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

            $user->roles()->syncWithoutDetaching([Role::CUSTOMER]);

            $address = ($user->address)?: new Address();
            $address->user_id = $user->id;
            $address->country_id = $data['country'];
            $address->type = Address::TYPE_BILLING;
            $address->zip = $data['zip'];
            $address->address = $data['address'];
            $address->number = $data['number'];
            $address->state = $data['state'];
            $address->city = $data['city'];
            $address->save();

            $customer->user_id = $user->id;
            $customer->phone = $data['phone'];
            $customer->document = $data['document'];
            $customer->birthday = $data['birthday'];
            $customer->gender = $data['gender'];
            $customer->news = $data['news'];
            $customer->terms = $data['terms'];
            $customer->save();
        });

        return $customer
            ->fresh()
            ->load('address')
            ->append(['name', 'email']);
    }

    /**
     * Update a customer
     *
     * @param array $data
     * @param Customer $customer
     * @return Customer
     * @throws Throwable
     */
    public function update(array $data, Customer $customer)
    {
        DB::transaction(function () use ($data, $customer) {

            $customer->user->name = $data['name'];
            if ($customer->user->email !== $data['email']) {

                $customer->user->email = $data['email'];
                $customer->user->checked_at = null;
            }
            $customer->user->save();

            $customer->address->country_id = $data['country'];
            $customer->address->zip = $data['zip'];
            $customer->address->address = $data['address'];
            $customer->address->number = $data['number'];
            $customer->address->state = $data['state'];
            $customer->address->city = $data['city'];
            $customer->address->save();

            $customer->phone = $data['phone'];
            $customer->document = $data['document'];
            $customer->birthday = $data['birthday'];
            $customer->gender = $data['gender'];
            $customer->news = $data['news'];
            $customer->terms = $data['terms'];
            $customer->save();
        });

        return $customer
            ->fresh()
            ->load('address')
            ->append(['name', 'email']);
    }

    /**
     * Delete a customer
     *
     * @param Customer $customer
     * @throws Throwable
     */
    public function delete(Customer $customer)
    {
        DB::transaction(function () use ($customer) {

            $customer->address->delete();
            $customer->user->roles()->detach(Role::CUSTOMER);
            $customer->delete();
        });
    }
}
