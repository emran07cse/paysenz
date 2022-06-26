<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PaymentRequestsTableSeeder::class);
        $this->call(BanksTableSeeder::class);
        $this->call(PaymentOptionsTableSeeder::class);
        //$this->call(PaymentOptionRatesTableSeeder::class);
    }
}
