<?php

use Illuminate\Database\Seeder;

class PaymentRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\PaymentRequest::class, 500)->make()->each(function ($request) {
            $request->save();
        });
    }
}
