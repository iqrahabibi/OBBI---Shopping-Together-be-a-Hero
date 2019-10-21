<?php

use Illuminate\Database\Seeder;
use App\Model\Role;
use App\Model\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_employee = Role::where('name', 'Herobi')->first();
        $role_manager  = Role::where('name', 'Administrator')->first();

        $employee = new User();
        $employee->fullname = 'Rudy';
        $employee->email = 'rudi@gmail.com';
        $employee->password = bcrypt('12345678');
        $employee->save();
        $employee->roles()->attach($role_employee);

        $manager = new User();
        $manager->fullname = 'Administrator';
        $manager->email = 'administrator@gmail.com';
        $manager->password = bcrypt('secret');
        $manager->save();
        $manager->roles()->attach($role_manager);
    }
}
