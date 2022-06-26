<?php

use Illuminate\Database\Seeder;

class PaymentOptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!(DB::table('payment_options')->count() > 0)){
            DB::table('payment_options')->insert([
                'id' => 1,
                'bank_id' => 1,
                'type' => 'Card',
                'name' => 'Visa',
                'icon_url' => 'images/icon_visa.png',
                'min_required_amount' => '2',
                'bank_charge_percentage' => '2',
            ]);
            DB::table('payment_options')->insert([
                'id' => 2,
                'bank_id' => 1,
                'type' => 'Card',
                'name' => 'MasterCard',
                'icon_url' => 'images/icon_master.png',
                'min_required_amount' => '2',
                'bank_charge_percentage' => '2',
            ]);
            DB::table('payment_options')->insert([
                'id' => 3,
                'bank_id' => 2,
                'type' => 'Card',
                'name' => 'Visa',
                'icon_url' => 'images/icon_visa.png',
                'min_required_amount' => '2',
                'bank_charge_percentage' => '2',
            ]);
            DB::table('payment_options')->insert([
                'id' => 4,
                'bank_id' => 2,
                'type' => 'Card',
                'name' => 'MasterCard',
                'icon_url' => 'images/icon_master.png',
                'min_required_amount' => '2',
                'bank_charge_percentage' => '2',
            ]);
        }
    }
}
