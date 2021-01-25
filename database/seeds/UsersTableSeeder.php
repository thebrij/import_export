<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@nowui.com',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now()
        ]);*/
        DB::table('users')->delete();
        User::create(array('name' => 'Mohsin Javeed', 'email' => 'mohsinmj@yahoo.com', 'role_id' => '1', 'password' => Hash::make('12345678')));
        User::create(array('name' => 'Test User 1', 'email' => 'testuser1@test.com', 'role_id' => '4', 'password' => Hash::make('12345678')));
    }
}
