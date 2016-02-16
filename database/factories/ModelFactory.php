<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Poop::class, function (Faker\Generator $faker) {
    $start = $faker->dateTimeBetween('-2 years', '-1 hour');
    $end = $faker->dateTimeBetween($start, 'now');

    return [
        'duration' => $faker->numberBetween(60, 1800),
        'start_at' => $start,
        'end_at' => $end
    ];
});
