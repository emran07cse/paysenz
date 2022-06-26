<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!(DB::table('roles')->count() > 0)){
            DB::table('roles')->insert([
                'id' => '1',
                'name' => 'Admin',
                'description' => 'Sees, Creates, Updates, Deletes Users and everything else.'
            ]);
            DB::table('roles')->insert([
                'id' => '2',
                'name' => 'Merchant',
                'description' => 'Merchant'
            ]);
        }
    }
}
