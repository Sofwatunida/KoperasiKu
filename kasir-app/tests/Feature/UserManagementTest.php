<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_user_management_pages(): void
    {
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_kasir_is_redirected_from_user_management_pages(): void
    {
        $this->actingAs($this->createUser(['role' => 'kasir']));

        $this->get(route('users.index'))->assertRedirect(route('dashboard'));
        $this->get(route('users.create'))->assertRedirect(route('dashboard'));
        $this->get(route('users.edit', $this->createUser()))->assertRedirect(route('dashboard'));
    }

    public function test_super_admin_can_open_user_management_pages(): void
    {
        $this->actingAs($this->createUser(['role' => 'super_admin']));

        $this->get(route('users.index'))->assertOk();
        $this->get(route('users.create'))->assertOk();
    }

    public function test_admin_can_list_users(): void
    {
        $admin = $this->createUser(['name' => 'Admin Utama', 'role' => 'admin']);
        $this->createUser(['name' => 'Kasir Satu', 'username' => 'kasir1', 'email' => 'kasir1@example.test']);

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Manajemen Pengguna')
            ->assertSee('Admin Utama')
            ->assertSee('Kasir Satu')
            ->assertSee('1 admin')
            ->assertSee('1 kasir');
    }

    public function test_admin_can_create_kasir_and_admin_accounts(): void
    {
        $this->actingAs($this->createUser(['role' => 'admin']));

        $this->post(route('users.store'), $this->userData(['username' => 'kasir_baru']))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['username' => 'kasir_baru', 'role' => 'kasir']);
        $this->assertTrue(Hash::check('password123', User::where('username', 'kasir_baru')->firstOrFail()->password));

        $this->post(route('users.store'), $this->userData([
            'name' => 'Admin Baru',
            'username' => 'admin_baru',
            'email' => 'admin_baru@example.test',
            'role' => 'admin',
        ]))->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['username' => 'admin_baru', 'role' => 'admin']);
    }

    public function test_admin_cannot_create_super_admin_accounts(): void
    {
        $this->actingAs($this->createUser(['role' => 'admin']));

        $this->post(route('users.store'), $this->userData([
            'username' => 'super_baru',
            'email' => 'super_baru@example.test',
            'role' => 'super_admin',
        ]))->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['username' => 'super_baru']);
    }

    public function test_creation_rejects_duplicate_username(): void
    {
        $this->actingAs($this->createUser(['role' => 'admin']));
        $this->createUser(['username' => 'kasir1']);

        $this->post(route('users.store'), $this->userData(['username' => 'kasir1']))
            ->assertSessionHasErrors('username');

        $this->assertDatabaseCount('users', 2);
    }

    public function test_admin_can_update_role_email_and_password(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $kasir = $this->createUser(['username' => 'kasir_lama', 'email' => 'lama@example.test']);

        $this->actingAs($admin)
            ->put(route('users.update', $kasir), $this->userData([
                'username' => 'kasir_lama',
                'email' => 'baru@example.test',
                'role' => 'admin',
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ]))
            ->assertRedirect(route('users.index'))
            ->assertSessionHasNoErrors();

        $kasir->refresh();
        $this->assertSame('admin', $kasir->role);
        $this->assertSame('baru@example.test', $kasir->email);
        $this->assertTrue(Hash::check('rahasia123', $kasir->password));
    }

    public function test_update_keeps_existing_password_when_left_blank(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $kasir = $this->createUser(['username' => 'kasir_tetap']);

        $this->actingAs($admin)
            ->put(route('users.update', $kasir), $this->userData([
                'username' => 'kasir_tetap',
                'email' => 'tetap@example.test',
                'password' => '',
                'password_confirmation' => '',
            ]))
            ->assertRedirect(route('users.index'));

        $this->assertTrue(Hash::check('password', $kasir->fresh()->password));
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->createUser(['role' => 'admin']);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_demote_or_delete_the_last_admin(): void
    {
        $admin = $this->createUser(['role' => 'admin']);

        $this->actingAs($admin)
            ->put(route('users.update', $admin), $this->userData([
                'username' => $admin->username,
                'email' => $admin->email,
                'role' => 'kasir',
            ]))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);
    }

    public function test_admin_cannot_manage_a_super_admin_account(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $superAdmin = $this->createUser([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'super@example.test',
            'role' => 'super_admin',
        ]);

        $this->actingAs($admin)->get(route('users.edit', $superAdmin))->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $superAdmin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    public function test_user_with_transaction_history_cannot_be_deleted(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $kasir = $this->createUser(['username' => 'kasir1']);

        Transaction::create([
            'transaction_code' => 'TRX-TEST-0001',
            'user_id' => $kasir->id,
            'total' => 30000,
            'paid' => 35000,
            'change' => 5000,
            'transaction_date' => now(),
        ]);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $kasir))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $kasir->id]);
    }

    public function test_admin_can_delete_kasir_without_transactions(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $kasir = $this->createUser(['username' => 'kasir1']);

        $this->actingAs($admin)
            ->delete(route('users.destroy', $kasir))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $kasir->id]);
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

    /**
     * @param  array<string, mixed>  $overrides
     * @return array{name: string, username: string, email: string, role: string, password: string, password_confirmation: string}
     */
    private function userData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Kasir Baru',
            'username' => 'kasir_baru',
            'email' => 'kasir_baru@example.test',
            'role' => 'kasir',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }
}
