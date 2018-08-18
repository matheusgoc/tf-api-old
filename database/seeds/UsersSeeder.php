<?php

use App\User;
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
    }
}
