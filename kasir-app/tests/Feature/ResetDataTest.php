<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_reset(): void
    {
        $this->delete(route('settings.reset'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_cannot_reset_data(): void
    {
        $product = $this->createProduct();

        $this->actingAs($this->createUser(['role' => 'admin']))
            ->delete(route('settings.reset'), ['confirmation' => 'RESET'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_kasir_cannot_reset_data(): void
    {
        $product = $this->createProduct();

        $this->actingAs($this->createUser(['role' => 'kasir']))
            ->delete(route('settings.reset'), ['confirmation' => 'RESET'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_reset_requires_the_confirmation_word(): void
    {
        $superAdmin = $this->createUser(['role' => 'super_admin']);
        $product = $this->createProduct();
        $this->createTransactionFor($superAdmin, $product);

        $this->actingAs($superAdmin)
            ->from(route('settings.index'))
            ->delete(route('settings.reset'), ['confirmation' => 'salah'])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasErrors('confirmation');

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('transaction_details', 1);
    }

    public function test_super_admin_can_reset_all_transaction_and_product_data(): void
    {
        $superAdmin = $this->createUser(['role' => 'super_admin']);
        $cashier = $this->createUser(['role' => 'kasir']);
        $product = $this->createProduct();
        $this->createTransactionFor($cashier, $product);

        $this->actingAs($superAdmin)
            ->delete(route('settings.reset'), ['confirmation' => 'RESET'])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseCount('transaction_details', 0);

        // Akun pengguna tidak boleh ikut terhapus.
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
        $this->assertDatabaseHas('users', ['id' => $cashier->id]);
    }

    private function createUser(array $overrides = []): User
    {
        static $counter = 0;
        $counter++;

        return User::create(array_merge([
            'name' => "User {$counter}",
            'username' => "user{$counter}",
            'email' => "user{$counter}@example.test",
            'password' => 'password',
            'role' => 'kasir',
        ], $overrides));
    }

    private function createProduct(): Product
    {
        return Product::create([
            'code' => 'P001',
            'name' => 'Kopi',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'unit' => 'pcs',
        ]);
    }

    private function createTransactionFor(User $user, Product $product): Transaction
    {
        $transaction = Transaction::create([
            'transaction_code' => 'TRX-001',
            'user_id' => $user->id,
            'total' => 15000,
            'paid' => 15000,
            'change' => 0,
            'transaction_date' => now(),
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 15000,
            'subtotal' => 15000,
        ]);

        return $transaction;
    }
}
