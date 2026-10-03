<?php

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    // Semua route aplikasi memakai middleware 'auth'.
    $this->actingAs(User::factory()->create());
});

it('loads the products page', function () {
    $this->get(route('products.index'))->assertStatus(200)->assertSee('Manajemen');
});

it('loads the create product page', function () {
    $this->get(route('products.create'))->assertStatus(200)->assertSee('Tambah Produk');
});

it('loads the edit product page', function () {
    $product = Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);

    $this->get(route('products.edit', $product->id))->assertStatus(200)->assertSee('Kopi');
});

it('loads the cashier page', function () {
    Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);
    $this->get(route('cashier.index'))->assertStatus(200)->assertSee('Kopi');
});

it('loads the dashboard with real product and transaction counts', function () {
    Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);

    $this->get(route('dashboard'))
        ->assertStatus(200)
        ->assertSee('KoperasiKu')
        ->assertSee('Total Produk');
});

it('stores a product', function () {
    $this->post(route('products.store'), ['name' => 'Teh', 'price' => 5000, 'stock' => 3])
        ->assertRedirect();

    $this->assertDatabaseHas('products', ['name' => 'Teh', 'stock' => 3]);
});

it('runs the full cashier flow', function () {
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
});

it('rejects checkout when payment is insufficient', function () {
    $product = Product::create(['name' => 'Kopi', 'price' => 15000, 'stock' => 10]);

    $cart = json_encode([['id' => $product->id, 'qty' => 1]]);

    $this->post(route('cashier.checkout'), [
        'cart' => $cart,
        'total_price' => 15000,
        'pay_amount' => 10000,
    ])->assertSessionHas('error');

    $this->assertDatabaseCount('transactions', 0);
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
});

it('lists transactions on the transactions page', function () {
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

    $transaction = Transaction::first();

    $this->get(route('transactions.show', $transaction->id))
        ->assertStatus(200)
        ->assertSee('Kopi');
});

it('cannot delete a product that has transaction history', function () {
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
});

it('can delete a product without transaction history', function () {
    $product = Product::create(['name' => 'Teh', 'price' => 5000, 'stock' => 3]);

    $this->delete(route('products.destroy', $product->id))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('requires login for the cashier page', function () {
    auth()->logout();

    $this->get(route('cashier.index'))->assertRedirect(route('login'));
});