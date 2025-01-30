<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('users')->insert([
            'name' => 'Caraka',
            'email' => 'caraka@gmail.com',
            'role' => 'caraka',
            'password' => Hash::make('123123123'),
        ]);

        DB::table('users')->insert([
            'name' => 'Reviewer',
            'email' => 'reviewer@gmail.com',
            'role' => 'reviewer',
            'password' => Hash::make('123123123'),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('123123123'),
        ]);
    }
}
