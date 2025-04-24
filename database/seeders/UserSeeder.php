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
            'name' => 'Raihan',
            'email' => 'r@r.com',
            'password' => bcrypt('r'),
        ]);

        User::create([
            'name' => 'Daffa',
            'email' => 'd@d.com',
            'password' => bcrypt('d'),
        ]);
    }
}
