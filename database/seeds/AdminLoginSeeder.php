<?php

use Illuminate\Database\Seeder;
use App\User;

class AdminLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'dummy@email.com',
            'password' => bcrypt('root'),
            'role' => 'admin',
            'skill' => 'admin',
            'profile_pic' => 'null'
        ]);
    }
}
