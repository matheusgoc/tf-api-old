<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'document' => $faker->randomDigit,
        'birthday' => $faker->date(),
        'gender' => $faker->randomElement(['M', 'F']),
        'phone' => $faker->phoneNumber,
        'news' => $faker->randomElement([true, false]),
        'terms' => true,
    ];
});
