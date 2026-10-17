<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarcodeScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_the_barcode_endpoint(): void
    {
        $this->get(route('cashier.scan', ['code' => 'P001']))
            ->assertRedirect(route('login'));
    }

    public function test_valid_barcode_returns_product_with_quantity_one(): void
    {
        $this->actingAs($this->createKasir());
        $product = Product::create($this->productData(['code' => 'P001', 'name' => 'Kopi', 'stock' => 5]));

        $this->getJson(route('cashier.scan', ['code' => 'P001', 'quantity' => 0]))
            ->assertOk()
            ->assertJson([
                'product' => ['id' => $product->id, 'code' => 'P001', 'name' => 'Kopi', 'stock' => 5],
                'quantity' => 1,
            ]);
    }

    public function test_scanning_the_same_product_again_increments_quantity(): void
    {
        $this->actingAs($this->createKasir());
        Product::create($this->productData(['code' => 'P008', 'name' => 'Map Plastik', 'stock' => 5]));

        $this->getJson(route('cashier.scan', ['code' => 'P008', 'quantity' => 1]))
            ->assertOk()
            ->assertJson(['quantity' => 2]);

        $this->getJson(route('cashier.scan', ['code' => 'P008', 'quantity' => 2]))
            ->assertOk()
            ->assertJson(['quantity' => 3]);
    }

    public function test_barcode_lookup_is_case_insensitive_and_read_only(): void
    {
        $this->actingAs($this->createKasir());
        $product = Product::create($this->productData(['code' => 'P001', 'stock' => 4]));

        $this->getJson(route('cashier.scan', ['code' => 'p001']))
            ->assertOk()
            ->assertJson(['product' => ['id' => $product->id]]);

        // Proses scan tidak boleh mengubah stok di database.
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 4]);
    }

    public function test_unknown_barcode_returns_not_found_message(): void
    {
        $this->actingAs($this->createKasir());

        $this->getJson(route('cashier.scan', ['code' => 'TIDAK-ADA']))
            ->assertNotFound()
            ->assertJson(['message' => 'Produk dengan barcode tersebut tidak ditemukan.']);
    }

    public function test_scan_is_rejected_when_quantity_reaches_maximum_stock(): void
    {
        $this->actingAs($this->createKasir());
        $product = Product::create($this->productData(['code' => 'P001', 'stock' => 5]));

        $this->getJson(route('cashier.scan', ['code' => 'P001', 'quantity' => 5]))
            ->assertStatus(422)
            ->assertJson([
                'message' => 'Jumlah produk sudah mencapai stok maksimum.',
                'quantity' => 5,
            ]);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    public function test_scan_requires_a_code(): void
    {
        $this->actingAs($this->createKasir());

        $this->getJson(route('cashier.scan', ['quantity' => 0]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('code');
    }

    private function createKasir(): User
    {
        return User::create([
            'name' => 'Kasir Satu',
            'username' => 'kasir1',
            'email' => 'kasir1@example.test',
            'password' => 'password',
            'role' => 'kasir',
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
