<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public const SATUAN = ['pcs', 'box', 'lusin', 'rim', 'botol', 'kg'];

    public function index(Request $request): View
    {
        $keyword = $request->string('search')->trim()->toString();
        $stokRendah = $request->boolean('stok_rendah');

        $products = Product::query()
            ->search($keyword)
            ->when($stokRendah, fn ($query) => $query->where('stock', '<=', Product::LOW_STOCK_THRESHOLD))
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'search' => $keyword,
            'totalProduk' => Product::query()->count(),
            'stokRendah' => $stokRendah,
        ]);
    }

    public function create(): View
    {
        return view('products.create', [
            'satuan' => self::SATUAN,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', [
            'product' => $product,
            'satuan' => self::SATUAN,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate($this->rules($product));

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->transactionDetails()->exists()) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak bisa dihapus karena sudah memiliki riwayat transaksi.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Aturan validasi form produk (dipakai bersama oleh store dan update).
     *
     * @return array<string, array<int, mixed>>
     */
    private function rules(?Product $product = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('products', 'code')->ignore($product?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', Rule::in(self::SATUAN)],
        ];
    }
}