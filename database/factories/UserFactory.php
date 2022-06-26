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

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\PaymentRequest::class, function (Faker $faker) {
    $name = $faker->firstName . ' ' . $faker->lastName;
    $email = $faker->email;
    $address = $faker->address;
    $phone = $faker->phoneNumber;
    return [
        'client_id' => 1,
        'order_id_of_merchant' => \Ramsey\Uuid\Uuid::uuid1()->toString(),
        'amount' => $faker->numberBetween(1, 10000),
        'currency_of_transaction' => 'BDT',
        'buyer_name' => $name,
        'buyer_email' => $email,
        'buyer_address' => $address,
        'buyer_contact_number' => $phone,
        'ship_to' => $name,
        'shipping_email' => $email,
        'shipping_address' => $address,
        'shipping_contact_number' => $phone,
        'order_details' => 'Test Order. Product ID: ' . $faker->bankAccountNumber,
        'callback_url' => $faker->url,
        'comma_separated_references' => $faker->colorName . ',' . $faker->colorName . ',' . $faker->colorName,
        'expected_response_type' => 'JSON',
        'status' => 'Initiated'
    ];
});

$factory->define(App\Bank::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'details' => $faker->text(200),
    ];
});

$factory->define(App\PaymentOption::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'details' => $faker->text(200),
    ];
});
