<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use App\EmpType;
use App\EmpGroup;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'staff_id' => $faker->unique()->numerify('IF##'),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'user_type' => $faker->randomElement(['Admin','Authority','Employee']),
        'emp_type_id' => EmpType::all()->random()->id,
        'emp_group_id' => EmpGroup::all()->random()->id,
        'gender' => $faker->randomElement(['Male','Female']),
        'job_title' => $faker->jobTitle,
        'join_date' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'emergency_contact_name' => $faker->name,
        'emergency_contact_no' => $faker->phoneNumber,
        'remember_token' => Str::random(10),
    ];
});
