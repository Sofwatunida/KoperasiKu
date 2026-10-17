<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use ZipArchive;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_every_export_route(): void
    {
        $this->get(route('reports.export.pdf'))->assertRedirect(route('login'));
        $this->get(route('reports.export.excel'))->assertRedirect(route('login'));
        $this->get(route('reports.export.word'))->assertRedirect(route('login'));
    }

    public function test_pdf_export_returns_a_valid_pdf_with_real_data(): void
    {
        $this->actingAs($this->createUser());
        $this->createTransaction('TRX-0001', now());

        $response = $this->get(route('reports.export.pdf', $this->periode()));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('content-disposition'));

        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
        $this->assertStringContainsString('LAPORAN PENJUALAN', $content);
        $this->assertStringContainsString('TRX-0001', $content);
        $this->assertStringContainsString('Kopi', $content);
    }

    public function test_pdf_export_only_contains_transactions_inside_the_period(): void
    {
        $this->actingAs($this->createUser());
        $this->createTransaction('TRX-BARU', Carbon::today());
        $this->createTransaction('TRX-LAMA', Carbon::today()->subYear());

        $content = $this->get(route('reports.export.pdf', $this->periode()))->getContent();

        $this->assertStringContainsString('TRX-BARU', $content);
        $this->assertStringNotContainsString('TRX-LAMA', $content);
    }

    public function test_excel_export_returns_a_valid_xlsx_package(): void
    {
        $this->actingAs($this->createUser());
        $this->createTransaction('TRX-0001', now());

        $response = $this->get(route('reports.export.excel', $this->periode()));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml.sheet', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));

        $content = $response->getContent();
        $this->assertStringStartsWith('PK', $content);

        $sheet = $this->readFromPackage($content, 'xl/worksheets/sheet1.xml');
        $this->assertNotNull($sheet);
        $this->assertStringContainsString('TRX-0001', $sheet);
        $this->assertStringContainsString('Kopi', $sheet);
        $this->assertStringContainsString('<v>30000</v>', $sheet);
    }

    public function test_word_export_returns_a_valid_docx_package(): void
    {
        $this->actingAs($this->createUser());
        $this->createTransaction('TRX-0001', now());

        $response = $this->get(route('reports.export.word', $this->periode()));

        $response->assertOk();
        $this->assertStringContainsString('wordprocessingml.document', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('.docx', (string) $response->headers->get('content-disposition'));

        $content = $response->getContent();
        $this->assertStringStartsWith('PK', $content);

        $document = $this->readFromPackage($content, 'word/document.xml');
        $this->assertNotNull($document);
        $this->assertStringContainsString('TRX-0001', $document);
        $this->assertStringContainsString('Kopi', $document);
        $this->assertStringContainsString('<w:tbl>', $document);
    }

    /**
     * @return array{dari: string, sampai: string}
     */
    private function periode(): array
    {
        return [
            'dari' => Carbon::today()->startOfMonth()->toDateString(),
            'sampai' => Carbon::today()->toDateString(),
        ];
    }

    private function createTransaction(string $code, Carbon $date): Transaction
    {
        $user = User::query()->first() ?? $this->createUser();
        $product = Product::query()->first() ?? Product::create([
            'code' => 'P001',
            'name' => 'Kopi',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'unit' => 'pcs',
        ]);

        $transaction = Transaction::create([
            'transaction_code' => $code,
            'user_id' => $user->id,
            'total' => 30000,
            'paid' => 35000,
            'change' => 5000,
            'transaction_date' => $date,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 15000,
            'subtotal' => 30000,
        ]);

        return $transaction;
    }

    private function readFromPackage(string $binary, string $entry): ?string
    {
        $temporary = tempnam(sys_get_temp_dir(), 'koperasiku-test-');
        file_put_contents($temporary, $binary);

        $archive = new ZipArchive;
        $this->assertTrue($archive->open($temporary) === true, 'Paket export tidak dapat dibuka.');

        $contents = $archive->getFromName($entry);
        $archive->close();
        @unlink($temporary);

        return $contents === false ? null : $contents;
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Kasir Satu',
            'username' => 'kasir1',
            'email' => 'kasir1@example.test',
            'password' => 'password',
            'role' => 'kasir',
        ]);
    }
}
