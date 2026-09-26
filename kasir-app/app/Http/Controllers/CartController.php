<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->get();

        return view('cashier.index', compact('products'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|json',
            'pay_amount' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
        ]);

        $cartData = json_decode($request->cart, true);

        if (empty($cartData)) {
            return redirect()->back()->with('error', 'Keranjang masih kosong.');
        }

        if ($request->pay_amount < $request->total_price) {
            return redirect()->back()->with('error', 'Uang pembayaran kurang!');
        }

        $transaction = DB::transaction(function () use ($request, $cartData) {
            // 1. Simpan Transaksi Utama
            $transaction = Transaction::create([
                'invoice_number' => 'INV-'.time(),
                'total_price' => $request->total_price,
                'pay_amount' => $request->pay_amount,
                'change_amount' => $request->pay_amount - $request->total_price,
            ]);

            // 2. Simpan Detail & Kurangi Stok
            foreach ($cartData as $item) {
                $product = Product::find($item['id']);

                if (! $product) {
                    continue;
                }

                if ($item['qty'] > $product->stock) {
                    throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                }

                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['qty'],
                ]);

                $product->decrement('stock', $item['qty']);
            }

            return $transaction;
        });

        return redirect()->route('cashier.receipt', $transaction->id);
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load('details.product');

        return view('cashier.receipt', compact('transaction'));
    }
}
