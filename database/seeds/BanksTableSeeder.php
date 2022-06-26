<?php

use Illuminate\Database\Seeder;

class BanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!(DB::table('banks')->count() > 0)){
            DB::table('banks')->insert([
                'id' => '1',
                'name' => 'The City Bank Limited',
                'short_code' => 'TCB',
                'details' => 'Some details'
            ]);
            DB::table('banks')->insert([
                'id' => '2',
                'name' => 'Dutch Bangla Bank Limited',
                'short_code' => 'DBBL',
                'details' => 'Some details'
            ]);
        }
    }
}
