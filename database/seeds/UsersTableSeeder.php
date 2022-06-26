<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!(DB::table('users')->count() > 0)){
            DB::table('users')->insert([
                'name' => 'Alex',
                'email' => 'admin@gmail.com',
                'phone' => '01234567891',
                'password' => bcrypt('test1234'),

                'tcb_id' => 'tcb123456',
                'dbbl_id' => 'dbbl123456',

                'role_id' => 1,
                'remember_token' => str_random(10),
            ]);

            //Content Providers
            DB::table('users')->insert([
                'name' => 'Karim International',
                'email' => 'merchant@gmail.com',
                'phone' => '01234567892',
                'password' => bcrypt('test1234'),

                'tcb_id' => 'tcb234561',
                'dbbl_id' => 'dbbl234561',

                'role_id' => 2,
                'remember_token' => str_random(10),
            ]);
        }
    }
}
