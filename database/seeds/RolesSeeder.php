<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Master
        $role = new Role();
        $role->id = Role::MASTER;
        $role->name = 'Master';
        $role->level = 40;
        $role->save();

        // Admin
        $role = new Role();
        $role->id = Role::ADMIN;
        $role->name = 'Admin';
        $role->level = 30;
        $role->save();

        // Staff
        $role = new Role();
        $role->id = Role::STAFF;
        $role->name = 'Staff';
        $role->level = 20;
        $role->save();

        // Customer
        $role = new Role();
        $role->id = Role::CUSTOMER;
        $role->name = 'Customer';
        $role->level = 10;
        $role->save();
    }
}
