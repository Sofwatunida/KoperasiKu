<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InitialAccountSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_the_three_initial_accounts_with_hashed_passwords(): void
    {
        $this->seed(UserSeeder::class);

        $accounts = [
            ['email' => 'akhwatgroup@gmail.com', 'role' => 'super_admin', 'password' => 'akhwatapp'],
            ['email' => 'adminku@gmail.com', 'role' => 'admin', 'password' => 'adminku08'],
            ['email' => 'koperasiku@gmail.com', 'role' => 'kasir', 'password' => 'koperasikuapp'],
        ];

        foreach ($accounts as $account) {
            $user = User::query()->where('email', $account['email'])->first();

            $this->assertNotNull($user, "Akun {$account['email']} tidak dibuat.");
            $this->assertSame($account['role'], $user->role);
            $this->assertNotSame($account['password'], $user->password, 'Password tidak boleh disimpan plaintext.');
            $this->assertTrue(Hash::check($account['password'], $user->password));
        }

        $this->assertSame(1, User::query()->where('email', 'akhwatgroup@gmail.com')->count());
    }

    public function test_seeder_is_idempotent_and_does_not_reset_existing_accounts(): void
    {
        $this->seed(UserSeeder::class);

        $superAdmin = User::query()->where('email', 'akhwatgroup@gmail.com')->firstOrFail();
        $superAdmin->update(['email' => 'superadminbaru@gmail.com', 'password' => 'passwordbaru123']);

        $this->seed(UserSeeder::class);

        $this->assertSame(0, User::query()->where('email', 'akhwatgroup@gmail.com')->count());
        $superAdmin->refresh();
        $this->assertSame('superadminbaru@gmail.com', $superAdmin->email);
        $this->assertSame('super_admin', $superAdmin->role);
        $this->assertTrue(Hash::check('passwordbaru123', $superAdmin->password));
    }

    public function test_every_initial_account_can_log_in_using_its_email(): void
    {
        $this->seed(UserSeeder::class);

        foreach (UserSeeder::ACCOUNTS as $account) {
            $this->post(route('login'), [
                'username' => $account['email'],
                'password' => $account['password'],
            ])->assertRedirect(route('dashboard'));

            $this->assertAuthenticatedAs(User::query()->where('email', $account['email'])->firstOrFail());

            $this->post(route('logout'))->assertRedirect(route('login'));
            $this->assertGuest();
        }
    }

    public function test_role_is_read_from_the_database_and_not_from_the_email(): void
    {
        // Email identik dengan akun awal, tetapi role di database adalah kasir.
        $user = User::create([
            'name' => 'Bukan Super Admin',
            'username' => 'bukan-super',
            'email' => 'akhwatgroup@gmail.com',
            'password' => 'password',
            'role' => 'kasir',
        ]);

        $this->assertTrue($user->isKasir());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->hasAdminAccess());

        $user->update(['email' => 'email-baru@example.test']);
        $this->assertSame('kasir', $user->fresh()->role);
    }
}
