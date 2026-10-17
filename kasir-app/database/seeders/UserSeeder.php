<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Akun awal sistem. Role selalu berasal dari kolom users.role,
     * bukan ditentukan dari alamat email.
     *
     * @var array<int, array{name: string, username: string, email: string, role: string, password: string}>
     */
    public const ACCOUNTS = [
        [
            'name' => 'Super Admin KoperasiKu',
            'username' => 'superadmin',
            'email' => 'akhwatgroup@gmail.com',
            'role' => User::ROLE_SUPER_ADMIN,
            'password' => 'akhwatapp',
        ],
        [
            'name' => 'Admin KoperasiKu',
            'username' => 'adminku',
            'email' => 'adminku@gmail.com',
            'role' => User::ROLE_ADMIN,
            'password' => 'adminku08',
        ],
        [
            'name' => 'Kasir KoperasiKu',
            'username' => 'koperasiku',
            'email' => 'koperasiku@gmail.com',
            'role' => User::ROLE_KASIR,
            'password' => 'koperasikuapp',
        ],
    ];

    public function run(): void
    {
        foreach (self::ACCOUNTS as $account) {
            // Jangan buat duplikat walau email sudah diganti user. Identifikasi
            // akun berdasarkan email ATAU username, dan jangan menimpa akun
            // yang sudah ada supaya email/password yang diganti tidak ter-reset.
            $exists = User::query()
                ->where(function ($query) use ($account): void {
                    $query->where('email', $account['email'])
                        ->orWhere('username', $account['username']);
                })
                ->exists();

            if ($exists) {
                continue;
            }

            User::create($account);
        }

        // Akun contoh lama tetap dipertahankan.
        User::query()->firstOrCreate(
            ['username' => 'siti'],
            [
                'name' => 'Siti Aisyah',
                'email' => 'siti@koperasiku.test',
                'role' => User::ROLE_KASIR,
                'password' => 'password',
            ],
        );

        User::query()->firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Budi Santoso',
                'email' => 'admin@koperasiku.test',
                'role' => User::ROLE_ADMIN,
                'password' => 'password',
            ],
        );
    }
}
