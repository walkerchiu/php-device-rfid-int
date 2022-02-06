<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\DeviceRFID\Models\Entities\Data;

$factory->define(Data::class, function (Faker $faker) {
    return [
        'reader_id'   => 1,
        'register_id' => 1,
        'card_id'     => 1,
        'identifier'  => '0000012345',
        'log'         => $faker->text,
        'trigger_at'  => '2019-01-01 01:00:00'
    ];
});
