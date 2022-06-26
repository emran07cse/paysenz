<?php

use Illuminate\Database\Seeder;

class PaymentOptionRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!(DB::table('payment_option_rates')->count() > 0)){
            DB::table('payment_option_rates')->insert([
                'id' => 1,
                'client_id' => 3,
                'payment_option_id' => 1,
                'paysenz_charge_percentage' => 3.5,
                'bank_charge_percentage' => 1.8,
            ]);
            DB::table('payment_option_rates')->insert([
                'id' => 2,
                'client_id' => 3,
                'payment_option_id' => 2,
                'paysenz_charge_percentage' => 3.5,
            ]);
            DB::table('payment_option_rates')->insert([
                'id' => 3,
                'client_id' => 3,
                'payment_option_id' => 3,
                'paysenz_charge_percentage' => 3.5,
            ]);
            DB::table('payment_option_rates')->insert([
                'id' => 4,
                'client_id' => 3,
                'payment_option_id' => 4,
                'paysenz_charge_percentage' => 3.5,
            ]);
        }
    }
}
