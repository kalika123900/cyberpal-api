<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Blog;
use Faker\Generator as Faker;

$factory->define(Blog::class, function (Faker $faker) {
    return [
        'isPublished' => true,
        'url' => $faker->uuid,
        'image' => $faker->image('public/storage/images',400,300, null, false),
        'title' => $faker->sentence($nbWords = 10),
        'content' => $faker->paragraph($nbSentences = 15),
    ];
});
