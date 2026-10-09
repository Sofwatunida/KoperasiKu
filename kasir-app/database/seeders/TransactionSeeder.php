<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Membuat riwayat transaksi contoh untuk mengisi Dashboard dan Laporan.
 * Transaksi disisipkan langsung ke database sehingga stok produk
 * tetap sama seperti hasil ProductSeeder.
 */
class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $kasir = User::query()->first();

        if (! $kasir) {
            return;
        }

        $products = Product::all();

        if ($products->isEmpty()) {
            return;
        }

        $pola = [
            [0, 2], [0, 1], [1, 3], [2, 1], [0, 2], [3, 4], [1, 1],
        ];

        foreach ($pola as $hariKe => [$indexA, $indexB]) {
            $tanggal = Carbon::now()->subDays($hariKe)->setTime(9 + $hariKe, 15);

            $total = 0;
            $items = [
                ['product' => $products[$indexA % $products->count()], 'quantity' => 2],
                ['product' => $products[$indexB % $products->count()], 'quantity' => 1],
            ];

            foreach ($items as $item) {
                $total += $item['product']->selling_price * $item['quantity'];
            }

            $paid = (int) (ceil($total / 5000) * 5000);

            $transaction = Transaction::create([
                'transaction_code' => 'TRX-'.str_pad((string) ($hariKe + 1), 4, '0', STR_PAD_LEFT),
                'user_id' => $kasir->id,
                'total' => $total,
                'paid' => $paid,
                'change' => $paid - $total,
                'transaction_date' => $tanggal,
            ]);

            foreach ($items as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['product']->selling_price,
                    'subtotal' => $item['product']->selling_price * $item['quantity'],
                ]);
            }
        }
    }
}