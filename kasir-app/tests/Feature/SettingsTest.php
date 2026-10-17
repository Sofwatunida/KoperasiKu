<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_open_the_account_settings_page(): void
    {
        $user = $this->createUser(['role' => 'kasir']);

        $this->actingAs($user)
            ->get(route('settings.index'))
            ->assertOk()
            ->assertSee('Pengaturan Akun')
            ->assertSee('Kasir');
    }

    public function test_user_can_change_email_without_changing_role(): void
    {
        $user = $this->createUser(['role' => 'super_admin']);

        $this->actingAs($user)
            ->put(route('settings.update'), [
                'name' => $user->name,
                'username' => $user->username,
                'email' => 'superadminbaru@gmail.com',
                'current_password' => 'password',
            ])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('superadminbaru@gmail.com', $user->email);
        $this->assertSame('super_admin', $user->role);
    }

    public function test_changing_email_requires_the_current_password(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->put(route('settings.update'), [
                'name' => $user->name,
                'username' => $user->username,
                'email' => 'baru@example.test',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_email_must_be_unique_and_show_indonesian_message(): void
    {
        $this->createUser(['username' => 'lain', 'email' => 'lain@example.test']);
        $user = $this->createUser();

        $this->actingAs($user)
            ->put(route('settings.update'), [
                'name' => $user->name,
                'username' => $user->username,
                'email' => 'lain@example.test',
                'current_password' => 'password',
            ])
            ->assertSessionHasErrors([
                'email' => 'Email sudah digunakan oleh akun lain.',
            ]);

        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->put(route('settings.update'), [
                'name' => $user->name,
                'username' => $user->username,
                'email' => 'baru@example.test',
                'current_password' => 'salah-sekali',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertSame($user->email, $user->fresh()->email);
    }

    public function test_user_can_change_password_and_login_with_the_new_password(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->put(route('settings.update'), [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'current_password' => 'password',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertSame('kasir', $user->role);

        $this->post(route('logout'));
        $this->post(route('login'), ['username' => $user->email, 'password' => 'rahasia123'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_confirmation_must_match(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->put(route('settings.update'), [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'current_password' => 'password',
                'password' => 'rahasia123',
                'password_confirmation' => 'beda12345',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_updating_only_the_name_does_not_require_the_current_password(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->put(route('settings.update'), [
                'name' => 'Nama Baru',
                'username' => $user->username,
                'email' => $user->email,
            ])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame('Nama Baru', $user->fresh()->name);
    }

    private function createUser(array $overrides = []): User
    {
        static $counter = 0;
        $counter++;

        return User::create(array_merge([
            'name' => "Kasir {$counter}",
            'username' => "kasir{$counter}",
            'email' => "kasir{$counter}@example.test",
            'password' => 'password',
            'role' => 'kasir',
        ], $overrides));
    }
}
