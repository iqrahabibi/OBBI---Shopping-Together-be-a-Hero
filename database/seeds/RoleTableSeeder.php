<?php

use Illuminate\Database\Seeder;
use App\Model\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_employee = new Role();
        $role_employee->name = 'Herobi';
        $role_employee->description = 'Akses sebagai Herobi';
        $role_employee->save();

        $role_manager = new Role();
        $role_manager->name = 'Administrator';
        $role_manager->description = 'Akses sebagai Administrator';
        $role_manager->save();
    }
}
