<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('users')->truncate();

        $this->call([ UsersTableSeeder::class]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');


        DB::table('roles')->truncate();

        Role::create(array('name' => 'Administrator', 'description' => 'System Administrator Role', 'status' => 'Enable', 'order' => '1', 'created_by' => '1', 'updated_by' => '1'));
        Role::create(array('name' => 'Manager'      , 'description' => 'System Manager Role'      , 'status' => 'Enable', 'order' => '2', 'created_by' => '1', 'updated_by' => '1'));
        Role::create(array('name' => 'Supervisor'   , 'description' => 'System Supervisor Role'   , 'status' => 'Enable', 'order' => '3', 'created_by' => '1', 'updated_by' => '1'));
        Role::create(array('name' => 'User'         , 'description' => 'System Genral User Role'  , 'status' => 'Enable', 'order' => '4', 'created_by' => '1', 'updated_by' => '1'));

    }
}
