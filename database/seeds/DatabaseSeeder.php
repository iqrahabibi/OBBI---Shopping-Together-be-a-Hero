<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(AddressSeeder::class);
        $this->call(CitySeeder::class);
        // $this->call(RoleTableSeeder::class);
        // $this->call(UserTableSeeder::class);
        // $this->call(MenuAndMenuChildrenSeeder::class);
    }
}
