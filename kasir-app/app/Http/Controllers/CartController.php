<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\JsonResponse;
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

    /**
     * Endpoint barcode scanner: cari produk dari database lalu tentukan
     * jumlah berikutnya pada keranjang, lengkap dengan validasi stok server-side.
     */
    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
        ], [
            'code.required' => __('cashier.barcode_required'),
        ]);

        $code = trim($validated['code']);
        $quantity = (int) ($validated['quantity'] ?? 0);

        $product = Product::query()
            ->whereRaw('UPPER(code) = ?', [mb_strtoupper($code)])
            ->first();

        if (! $product) {
            return response()->json([
                'message' => __('cashier.scan_not_found'),
            ], 404);
        }

        if ($quantity >= $product->stock) {
            return response()->json([
                'message' => __('cashier.scan_stock_max'),
                'product' => $this->productPayload($product),
                'quantity' => $quantity,
            ], 422);
        }

        return response()->json([
            'message' => __('cashier.scan_added', ['name' => $product->name]),
            'product' => $this->productPayload($product),
            'quantity' => $quantity + 1,
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
            'cart.required' => __('cashier.cart_empty'),
            'cart.min' => __('cashier.cart_empty'),
            'paid.required' => __('cashier.paid_required'),
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
                        throw new RuntimeException(__('cashier.stock_insufficient', ['name' => $product->name]));
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
                    throw new RuntimeException(__('cashier.cart_empty'));
                }

                $paid = (int) $validated['paid'];

                if ($paid < $total) {
                    throw new RuntimeException(__('cashier.paid_insufficient'));
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
            ->with('success', __('cashier.checkout_success'));
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

    /**
     * Data produk yang dikirim ke frontend saat barcode terbaca.
     *
     * @return array<string, int|string>
     */
    private function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'code' => $product->code,
            'name' => $product->name,
            'price' => $product->selling_price,
            'stock' => $product->stock,
            'unit' => $product->unit,
        ];
    }
}
