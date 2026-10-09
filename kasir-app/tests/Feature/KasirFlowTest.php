<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KasirFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_login_and_is_redirected_from_application_pages(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('KoperasiKu');
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_authenticated_pages_load(): void
    {
        $this->actingAs($this->createCashier());

        $this->get(route('dashboard'))->assertOk()->assertSee('Grafik Penjualan');
        $this->get(route('products.index'))->assertOk()->assertSee('Data Produk');
        $this->get(route('products.create'))->assertOk()->assertSee('Tambah Produk Baru');
        $this->get(route('cashier.index'))->assertOk()->assertSee('Keranjang');
        $this->get(route('transactions.index'))->assertOk()->assertSee('Riwayat Transaksi');
        $this->get(route('reports.index'))->assertOk()->assertSee('Laporan Penjualan');
        $this->get(route('reports.print'))->assertOk()->assertSee('KOPERASIKU');
        $this->get(route('settings.index'))->assertOk()->assertSee('Pengaturan Akun');
    }

    public function test_low_stock_filter_only_lists_products_at_or_below_the_threshold(): void
    {
        $this->actingAs($this->createCashier());
        Product::create($this->productData(['code' => 'P001', 'name' => 'Stok rendah', 'stock' => 5]));
        Product::create($this->productData(['code' => 'P002', 'name' => 'Stok cukup', 'stock' => 20]));

        $this->get(route('products.index', ['stok_rendah' => 1]))
            ->assertOk()
            ->assertSee('Stok rendah')
            ->assertDontSee('Stok cukup');
    }

    public function test_user_can_sign_in_and_sign_out(): void
    {
        $user = $this->createCashier();

        $this->post(route('login'), ['username' => $user->username, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guest_can_register_a_petugas_account(): void
    {
        $this->get(route('register'))->assertOk()->assertSee('Buat akun baru');

        $this->post(route('register.store'), [
            'name' => 'Dewi Lestari',
            'username' => 'dewi_lestari',
            'email' => 'dewi@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $user = User::query()->where('username', 'dewi_lestari')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('petugas', $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_registration_rejects_duplicate_usernames(): void
    {
        $this->createCashier();

        $this->post(route('register.store'), [
            'name' => 'Dewi Lestari',
            'username' => 'siti',
            'email' => 'dewi@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('username');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_report_rejects_a_date_range_that_ends_before_it_starts(): void
    {
        $this->actingAs($this->createCashier());

        $this->get(route('reports.index', ['dari' => '2026-10-10', 'sampai' => '2026-10-09']))
            ->assertSessionHasErrors('sampai');
    }

    public function test_user_can_create_and_update_a_product(): void
    {
        $this->actingAs($this->createCashier());

        $this->post(route('products.store'), $this->productData())
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $product = Product::query()->firstOrFail();
        $this->assertDatabaseHas('products', ['code' => 'P001', 'name' => 'Kopi', 'selling_price' => 15000, 'stock' => 10]);

        $this->put(route('products.update', $product), $this->productData(['code' => 'P002', 'name' => 'Kopi Susu']))
            ->assertRedirect(route('products.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Kopi Susu']);
    }

    public function test_checkout_saves_transaction_details_and_reduces_stock(): void
    {
        $this->actingAs($this->createCashier());
        $product = Product::create($this->productData());

        $this->post(route('cashier.checkout'), [
            'cart' => [['id' => $product->id, 'quantity' => 2]],
            'paid' => 35000,
        ])->assertRedirect();

        $transaction = Transaction::first();
        $this->assertNotNull($transaction);
        $this->assertEquals(30000, $transaction->total);
        $this->assertEquals(5000, $transaction->change);

        $this->assertDatabaseHas('transaction_details', [
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 15000,
            'subtotal' => 30000,
        ]);

        $this->get(route('cashier.receipt', $transaction->id))
            ->assertOk()
            ->assertSee('KOPERASIKU')
            ->assertSee('Kopi');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);
    }

    public function test_checkout_rejected_when_payment_insufficient(): void
    {
        $this->actingAs($this->createCashier());
        $product = Product::create($this->productData());

        $this->post(route('cashier.checkout'), [
            'cart' => [['id' => $product->id, 'quantity' => 1]],
            'paid' => 10000,
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    }

    public function test_checkout_rejected_when_quantity_exceeds_stock(): void
    {
        $this->actingAs($this->createCashier());
        $product = Product::create($this->productData(['stock' => 1]));

        $this->post(route('cashier.checkout'), [
            'cart' => [['id' => $product->id, 'quantity' => 2]],
            'paid' => 50000,
        ])->assertSessionHas('error');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    }

    public function test_checkout_combines_duplicate_product_rows_before_checking_stock(): void
    {
        $this->actingAs($this->createCashier());
        $product = Product::create($this->productData(['stock' => 1]));

        $this->post(route('cashier.checkout'), [
            'cart' => [
                ['id' => $product->id, 'quantity' => 1],
                ['id' => $product->id, 'quantity' => 1],
            ],
            'paid' => 50000,
        ])->assertSessionHas('error', 'Stok Kopi tidak mencukupi.');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    }

    private function createCashier(): User
    {
        return User::create([
            'name' => 'Siti Aisyah',
            'username' => 'siti',
            'email' => 'siti@example.test',
            'password' => 'password',
            'role' => 'petugas',
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
