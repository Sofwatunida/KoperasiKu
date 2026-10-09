<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class CartController extends Controller
{
    public function index(): View
    {
        $keyword = request()->string('search')->trim()->toString();

        $products = Product::query()
            ->search($keyword)
            ->orderBy('code')
            ->get();

        return view('cashier.index', [
            'products' => $products,
            'search' => $keyword,
        ]);
    }

    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.id' => ['required', 'integer', 'exists:products,id'],
            'cart.*.quantity' => ['required', 'integer', 'min:1'],
            'paid' => ['required', 'integer', 'min:0'],
        ], [
            'cart.required' => 'Keranjang masih kosong.',
            'cart.min' => 'Keranjang masih kosong.',
            'paid.required' => 'Uang pembayaran wajib diisi.',
        ]);

        $items = collect($validated['cart'])
            ->groupBy('id')
            ->map(fn ($productItems, $id) => [
                'id' => (int) $id,
                'quantity' => (int) $productItems->sum('quantity'),
            ])
            ->values();

        try {
            $transaction = DB::transaction(function () use ($items, $validated) {
                $products = Product::query()
                    ->whereIn('id', $items->pluck('id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $total = 0;
                $rows = [];

                foreach ($items as $item) {
                    $product = $products->get($item['id']);

                    if (! $product) {
                        continue;
                    }

                    if ($item['quantity'] > $product->stock) {
                        throw new RuntimeException("Stok {$product->name} tidak mencukupi.");
                    }

                    $subtotal = $product->selling_price * $item['quantity'];
                    $total += $subtotal;

                    $rows[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ];
                }

                if ($rows === []) {
                    throw new RuntimeException('Keranjang masih kosong.');
                }

                $paid = (int) $validated['paid'];

                if ($paid < $total) {
                    throw new RuntimeException('Jumlah pembayaran tidak mencukupi.');
                }

                $transaction = Transaction::create([
                    'transaction_code' => self::kodeBerikutnya(),
                    'user_id' => Auth::id(),
                    'total' => $total,
                    'paid' => $paid,
                    'change' => $paid - $total,
                    'transaction_date' => Carbon::now(),
                ]);

                foreach ($rows as $row) {
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $row['product']->id,
                        'quantity' => $row['quantity'],
                        'price' => $row['price'],
                        'subtotal' => $row['subtotal'],
                    ]);

                    // Stok benar-benar dikurangi di database.
                    $row['product']->decrement('stock', $row['quantity']);
                }

                return $transaction;
            });
        } catch (RuntimeException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('cashier.receipt', $transaction->id)
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    public function receipt(Transaction $transaction): View
    {
        $transaction->load(['details.product', 'user']);

        return view('cashier.receipt', compact('transaction'));
    }

    /**
     * Nomor transaksi berikutnya, contoh: TRX-0005.
     */
    private static function kodeBerikutnya(): string
    {
        $nextId = (int) Transaction::max('id') + 1;

        return 'TRX-'.Str::padLeft((string) $nextId, 4, '0');
    }
}