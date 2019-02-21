<?php

use \Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\Hash;

class PeopleSeeder extends Seeder
{

    public function run()
    {
        DB::table('people')->delete();

        DB::table('people')->insert([
            [
                'id' => 1,
                'firstName' => 'Admin',
                'lastName' => 'Administrator',
                'salary' => 1000.30,
                'login' => 'admin',
                'password' => Hash::make('admin'),
                'token' => str_random(60),
                'super' => true,
                'tenant_id' => null,
            ],[
                'id' => 2,
                'firstName' => 'Tenant1',
                'lastName' => 'Tenant1 Last Name',
                'salary' => 2000.50,
                'login' => 'tenant1',
                'password' => Hash::make('test1'),
                'token' => str_random(60),
                'super' => false,
                'tenant_id' => 1,
            ],[
                'id' => 3,
                'firstName' => 'Tenant2',
                'lastName' => 'Tenant2 Last Name',
                'login' => 'tenant2',
                'salary' => 3000,
                'password' => Hash::make('test2'),
                'token' => str_random(60),
                'super' => false,
                'tenant_id' => 1
            ],[
                'id' => 4,
                'firstName' => 'SubTenant1',
                'lastName' => 'SubTenant1 Last Name',
                'salary' => 4000.44,
                'login' => 'SubTenant1',
                'password' => Hash::make('test3'),
                'token' => str_random(60),
                'super' => false,
                'tenant_id' => 2
            ]
        ]);
    }
}
