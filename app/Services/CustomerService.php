<?php


namespace App\Services;


use Illuminate\Http\Request;
use Throwable;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerService
{
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

    public function get(Customer $customer)
    {
        return $customer
            ->load('address')
            ->append(['name', 'email']);
    }

    /**
     * Generate a new customer
     *
     * @param Request $request
     * @param User|null $user
     * @return Customer
     * @throws Throwable
     */
    public function create(Request $request, User $user = null)
    {
        $customer = ($user && $user->customer)? $user->customer : new Customer();
        DB::transaction(function () use ($request, $customer, $user) {

            if (!$user) {

                $user = new User();
                $user->password = bcrypt($request->get('password'));
            }
            if ($user->email !== $request->get('email')) {

                $user->email = $request->get('email');
                $user->checked_at = null;
            }
            $user->name = $request->get('name');
            $user->save();

            $user->roles()->syncWithoutDetaching([Role::CUSTOMER]);

            $address = ($user->address)?: new Address();
            $address->user_id = $user->id;
            $address->country_id = $request->get('country');
            $address->type = Address::TYPE_BILLING;
            $address->zip = $request->get('zip');
            $address->address = $request->get('address');
            $address->number = $request->get('number');
            $address->state = $request->get('state');
            $address->city = $request->get('city');
            $address->save();

            $customer->user_id = $user->id;
            $customer->phone = $request->get('phone');
            $customer->document = $request->get('document');
            $customer->birthday = $request->get('birthday');
            $customer->gender = $request->get('gender');
            $customer->news = $request->get('news');
            $customer->terms = $request->get('terms');
            $customer->save();
        });

        return $customer
            ->fresh()
            ->load('address')
            ->append(['name', 'email']);
    }

    /**
     * Update customer
     *
     * @param Request $request
     * @param Customer $customer
     * @return Customer
     * @throws Throwable
     */
    public function update(Request $request, Customer $customer)
    {
        DB::transaction(function () use ($request, $customer) {

            $customer->user->name = $request->get('name');
            if ($customer->user->email !== $request->get('email')) {

                $customer->user->email = $request->get('email');
                $customer->user->checked_at = null;
            }

            $customer->address->country_id = $request->get('country_id');
            $customer->address->zip = $request->get('zip');
            $customer->address->address = $request->get('address');
            $customer->address->number = $request->get('number');
            $customer->address->state = $request->get('state');
            $customer->address->city = $request->get('city');

            $customer->phone = $request->get('phone');
            $customer->document = $request->get('document');
            $customer->birthday = $request->get('birthday');
            $customer->gender = $request->get('gender');
            $customer->news = $request->get('news');
            $customer->terms = $request->get('terms');

            $customer->save();
        });

        return $customer
            ->fresh()
            ->load('address')
            ->append(['name', 'email']);
    }


    public function delete(Customer $customer)
    {
        DB::transaction(function () use ($customer) {

            $customer->address->delete();
            $customer->user->roles()->detach(Role::CUSTOMER);
            $customer->delete();
        });
    }
}
