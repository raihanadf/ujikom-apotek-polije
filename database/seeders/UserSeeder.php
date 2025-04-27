<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'r@r.com',
            'password' => bcrypt('r'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Apoteker',
            'email' => 'a@a.com',
            'password' => bcrypt('a'),
            'role' => 'apoteker',
        ]);

        User::create([
            'name' => 'Pelanggan',
            'email' => 'p@p.com',
            'password' => bcrypt('p'),
            'CustomerId' => "PLG001",
            'role' => 'pelanggan',
        ]);
    }
}
