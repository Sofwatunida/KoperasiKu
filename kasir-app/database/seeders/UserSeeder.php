<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'siti'],
            [
                'name' => 'Siti Aisyah',
                'email' => 'siti@koperasiku.test',
                'role' => 'petugas',
                'password' => 'password',
            ],
        );

        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Budi Santoso',
                'email' => 'admin@koperasiku.test',
                'role' => 'admin',
                'password' => 'password',
            ],
        );
    }
}