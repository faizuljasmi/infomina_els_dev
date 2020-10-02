<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\EmpType;
use Faker\Generator as Faker;

$factory->define(EmpType::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['Executive','Non Executive','Trainee','Intern','Contract']),
    ];
});
