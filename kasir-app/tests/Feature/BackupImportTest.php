<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BackupImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_backup_and_import(): void
    {
        $this->get(route('settings.backup'))->assertRedirect(route('login'));
        $this->post(route('settings.import'))->assertRedirect(route('login'));
    }

    public function test_admin_cannot_download_backup(): void
    {
        $this->actingAs($this->createUser(['role' => 'admin']))
            ->get(route('settings.backup'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_kasir_cannot_import_data(): void
    {
        $this->actingAs($this->createUser(['role' => 'kasir']))
            ->post(route('settings.import'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_super_admin_backup_downloads_json_with_all_records(): void
    {
        $superAdmin = $this->createUser(['role' => 'super_admin']);
        $product = $this->createProduct();
        $this->createTransactionFor($superAdmin, $product);

        $response = $this->actingAs($superAdmin)->get(route('settings.backup'));

        $response->assertOk();
        $this->assertStringContainsString('application/json', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('.json', (string) $response->headers->get('content-disposition'));

        $payload = json_decode($response->getContent(), true);

        $this->assertIsArray($payload);
        $this->assertCount(1, $payload['users']);
        $this->assertCount(1, $payload['products']);
        $this->assertCount(1, $payload['transactions']);
        $this->assertSame('Kopi', $payload['products'][0]['name']);
        $this->assertSame('TRX-IMP-1', $payload['transactions'][0]['transaction_code']);
        $this->assertCount(1, $payload['transactions'][0]['details']);
    }

    public function test_import_restores_data_from_a_backup_file(): void
    {
        $superAdmin = $this->createUser(['role' => 'super_admin']);
        $product = $this->createProduct();
        $this->createTransactionFor($superAdmin, $product);

        // Unduh backup dulu, lalu kosongkan semua data sebelumimpor kembali.
        $backup = $this->actingAs($superAdmin)->get(route('settings.backup'))->getContent();

        TransactionDetail::query()->delete();
        Transaction::query()->delete();
        Product::query()->delete();
        User::query()->delete();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('transactions', 0);

        $file = UploadedFile::fake()->createWithContent('backup.json', $backup);

        $this->actingAs($superAdmin)
            ->post(route('settings.import'), ['backup_file' => $file])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('transactions', 1);
        $this->assertDatabaseCount('transaction_details', 1);
        $this->assertDatabaseHas('products', ['name' => 'Kopi']);
    }

    public function test_import_rejects_a_file_that_is_not_valid_json(): void
    {
        $superAdmin = $this->createUser(['role' => 'super_admin']);
        $file = UploadedFile::fake()->createWithContent('backup.json', 'not-json');

        $this->actingAs($superAdmin)
            ->from(route('settings.index'))
            ->post(route('settings.import'), ['backup_file' => $file])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasErrors('backup_file');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_import_rejects_a_backup_with_missing_sections(): void
    {
        $superAdmin = $this->createUser(['role' => 'super_admin']);
        $file = UploadedFile::fake()->createWithContent('backup.json', json_encode(['users' => []]));

        $this->actingAs($superAdmin)
            ->from(route('settings.index'))
            ->post(route('settings.import'), ['backup_file' => $file])
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasErrors('backup_file');
    }

    public function test_import_requires_a_file_to_be_uploaded(): void
    {
        $superAdmin = $this->createUser(['role' => 'super_admin']);

        $this->actingAs($superAdmin)
            ->from(route('settings.index'))
            ->post(route('settings.import'))
            ->assertRedirect(route('settings.index'))
            ->assertSessionHasErrors('backup_file');
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
            'transaction_code' => 'TRX-IMP-1',
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
