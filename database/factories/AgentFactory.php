<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Agent;
use Faker\Generator as Faker;

$factory->define(Agent::class, function (Faker $faker) {
    return [
        'nickname' => $faker->name,
        'birthday' => $faker->date(),
        'phone' => $faker->phoneNumber,
    ];
});
