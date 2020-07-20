<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use App\User;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory('App\User')->create()->id;
        },
        'name' => $faker->sentence(4),
        'brand' => $faker->sentence(4),
        'image' => $faker->image,
        'description' => $faker->sentence(4),
        'price' => $faker->randomDigit(),
        'user_id' => factory(User::class)
    ];
});
