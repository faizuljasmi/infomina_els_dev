<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\LeaveType;
use Faker\Generator as Faker;

$factory->define(LeaveType::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
