<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Taoke\Pid::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'member_id' => rand(1, 50),
        'pid' => $faker->uuid,
        'type' => array_rand([1, 2]),
    ];
});

$factory->define(App\Models\Taoke\Order::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'member_id' => rand(1, 50),
        'group_id' => rand(1, 50),
        'ordernum' => $faker->randomNumber(),
        'title' => $faker->title,
        'itemid' => rand(1, 100),
        'count' => rand(1, 10),
        'price' => $faker->numberBetween(2, 500),
        'final_price' => $faker->numberBetween(2, 500),
        'commission_rate' => rand(10, 999),
        'commission_amount' => $faker->numberBetween(2, 100),
        'pid' => $faker->uuid,
        'status' => array_rand([1,2,3,4,5]),
        'type' => array_rand([1, 2,3]),
        'complete_at' => now(),
    ];
});

$factory->define(App\Models\Taoke\Quan::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'item_id' => rand(1, 50),
        'nickname' => $faker->title,
        'headimg' => $faker->imageUrl(100, 100),
        'introduce' => $faker->text,
        'images' => json_encode([
            $faker->imageUrl(400, 400),
            $faker->imageUrl(400, 400),
            $faker->imageUrl(400, 400),
            $faker->imageUrl(400, 400),
        ]),
        'comments' => json_encode([
            $faker->title,
            $faker->title,
            $faker->title,
            $faker->title,
        ]),
        'taokouling' => array_rand([1, 0]),
        'shares' => rand(10, 999),
        'share_at' => $faker->date('Y-m-d H:i:s'),
        'type' => array_rand([1,2,3,4]),
    ];
});