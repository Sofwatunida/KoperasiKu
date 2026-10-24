<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_bulk_delete(): void
    {
        $this->delete(route('products.bulk-destroy'))
            ->assertRedirect(route('login'));
    }

    public function test_selected_products_are_deleted(): void
    {
        $this->actingAs($this->createAdmin());
        $first = Product::create($this->productData(['code' => 'P001']));
        $second = Product::create($this->productData(['code' => 'P002']));
        $untouched = Product::create($this->productData(['code' => 'P003']));

        $this->delete(route('products.bulk-destroy'), ['product_ids' => [$first->id, $second->id]])
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $first->id]);
        $this->assertDatabaseMissing('products', ['id' => $second->id]);
        $this->assertDatabaseHas('products', ['id' => $untouched->id]);
    }

    public function test_products_with_transaction_history_are_skipped(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $blocked = Product::create($this->productData(['code' => 'P001']));
        $deletable = Product::create($this->productData(['code' => 'P002']));

        $transaction = Transaction::create([
            'transaction_code' => 'TRX-001',
            'user_id' => $admin->id,
            'total' => 15000,
            'paid' => 15000,
            'change' => 0,
            'transaction_date' => now(),
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $blocked->id,
            'quantity' => 1,
            'price' => 15000,
            'subtotal' => 15000,
        ]);

        $this->delete(route('products.bulk-destroy'), ['product_ids' => [$blocked->id, $deletable->id]])
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', ['id' => $blocked->id]);
        $this->assertDatabaseMissing('products', ['id' => $deletable->id]);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        $this->actingAs($this->createAdmin());

        $this->from(route('products.index'))
            ->delete(route('products.bulk-destroy'), ['product_ids' => []])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasErrors('product_ids');
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin Satu',
            'username' => 'admin1',
            'email' => 'admin1@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
    }

    private function productData(array $overrides = []): array
    {
        return array_merge([
            'code' => 'P001',
            'name' => 'Kopi',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'unit' => 'pcs',
        ], $overrides);
    }
}
