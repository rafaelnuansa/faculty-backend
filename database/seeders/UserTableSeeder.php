<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //create data user
        User::create([
            'name'      => 'BDKM',
            'email'     => 'bdkm@unida.ac.id',
            'password'  => bcrypt('password')
        ]);

    }
}
