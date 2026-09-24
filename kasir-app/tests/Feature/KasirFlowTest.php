<?php

namespace Tests\Feature;

use App\Models\Product;
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

        $this->post(route('cashier.add', $product->id))->assertRedirect();

        $this->post(route('cashier.checkout'), ['pay_amount' => 20000])
            ->assertRedirect();

        $transaction = \App\Models\Transaction::first();
        $this->assertNotNull($transaction);
        $this->assertEquals(15000, $transaction->total_price);
        $this->assertEquals(5000, $transaction->change_amount);

        $this->get(route('cashier.receipt', $transaction->id))
            ->assertStatus(200)
            ->assertSee('Kopi');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 9]);
    }
}