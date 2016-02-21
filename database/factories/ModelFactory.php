<?php

use App\Entry;

$factory->define(Entry::class, function (Faker\Generator $faker) {
    $start = $faker->dateTimeBetween('-2 years', '-1 hour');
    $end = $faker->dateTimeBetween($start, 'now');

    return [
        'type' =>   $faker->randomElement(['break', 'bathroom', 'work', 'drinking']),
        'duration' => $faker->numberBetween(60, 1800),
        'start_at' => $start,
        'end_at' => $end
    ];
});
