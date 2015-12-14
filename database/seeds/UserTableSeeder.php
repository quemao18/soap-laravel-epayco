<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder  
{
    
    public function run()
    {
        User::create([
            'id' => '1',
            'type' => 'super',
            'name' => 'Roy & Co',
            'email' => 'hi@royand.co',
            'password' => 'a954d1b7e61ff31042e693518b3e3bfa179af3cc104a625906abce421b663c92',
            // Developer2015
        ]);
    }

}