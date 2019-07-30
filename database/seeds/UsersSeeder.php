<?php

use App\Models\Address;
use App\Models\Agent;
use App\Models\Country;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Matheus Câmara';
        $user->email = 'matheus.goc@gmail.com';
        $user->password = bcrypt('video');
        $user->save();
        $user->roles()->attach(Role::MASTER);

        $address = new Address();
        $address->user_id = $user->id;
        $address->country_id = Country::BRA;
        $address->type = Address::TYPE_AGENT;
        $address->zip = '70766060';
        $address->address = 'SQN 313 BLOCO F';
        $address->number = '104';
        $address->state = 'DF';
        $address->city = 'Brasília';
        $address->save();

        $agent = new Agent();
        $agent->user_id = $user->id;
        $agent->nickname = 'Matias';
        $agent->birthday = '1985-09-04';
        $agent->phone = '5561996831804';
        $agent->save();

    }
}
