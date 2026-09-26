<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasirFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_page_loads(): void
    {
        $this->get(route('products.index'))->assertStatus(200)->assertSee('Manajemen');
    }

    public function test_cashier_page_loads(): void
    {
        Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);
        $this->get(route('cashier.index'))->assertStatus(200)->assertSee('Kopi');
    }

    public function test_store_product(): void
    {
        $this->post(route('products.store'), ['name' => 'Teh', 'price' => 5000, 'stock' => 3])
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['name' => 'Teh', 'stock' => 3]);
    }

    public function test_full_cashier_flow(): void
    {
        $product = Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);

        $cart = json_encode([
            ['id' => $product->id, 'name' => 'Kopi', 'price' => 15000, 'qty' => 1],
        ]);

        $this->post(route('cashier.checkout'), [
            'cart' => $cart,
            'total_price' => 15000,
            'pay_amount' => 20000,
        ])->assertRedirect();

        $transaction = Transaction::first();
        $this->assertNotNull($transaction);
        $this->assertEquals(15000, $transaction->total_price);
        $this->assertEquals(5000, $transaction->change_amount);

        $this->assertDatabaseHas('transaction_details', [
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 15000,
            'subtotal' => 15000,
        ]);

        $this->get(route('cashier.receipt', $transaction->id))
            ->assertStatus(200)
            ->assertSee('Kopi');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 9]);
    }

    public function test_checkout_rejected_when_payment_insufficient(): void
    {
        $product = Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);

        $cart = json_encode([['id' => $product->id, 'qty' => 1]]);

        $this->post(route('cashier.checkout'), [
            'cart' => $cart,
            'total_price' => 15000,
            'pay_amount' => 10000,
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    }

    public function test_transactions_page_lists_transactions(): void
    {
        $product = Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);

        $cart = json_encode([['id' => $product->id, 'qty' => 1]]);

        $this->post(route('cashier.checkout'), [
            'cart' => $cart,
            'total_price' => 15000,
            'pay_amount' => 20000,
        ])->assertRedirect();

        $this->get(route('transactions.index'))
            ->assertStatus(200)
            ->assertSee('Kopi');
    }

    public function test_product_with_transaction_history_cannot_be_deleted(): void
    {
        $product = Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);

        $cart = json_encode([['id' => $product->id, 'qty' => 1]]);

        $this->post(route('cashier.checkout'), [
            'cart' => $cart,
            'total_price' => 15000,
            'pay_amount' => 20000,
        ])->assertRedirect();

        $this->delete(route('products.destroy', $product->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_product_without_transactions_can_be_deleted(): void
    {
        $product = Product::create(['name' => 'Teh', 'price' => 5000, 'stock' => 3]);

        $this->delete(route('products.destroy', $product->id))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
