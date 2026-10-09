<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today()->locale('id');

        $totalProduk = Product::query()->count();
        $totalStok = (int) Product::query()->sum('stock');
        $transaksiHariIni = Transaction::query()
            ->whereDate('transaction_date', $today->toDateString())
            ->count();
        $pendapatanHariIni = (int) Transaction::query()
            ->whereDate('transaction_date', $today->toDateString())
            ->sum('total');

        $grafik = collect(range(6, 0))->map(function (int $mundur) use ($today) {
            $tanggal = $today->copy()->subDays($mundur);

            return [
                'label' => $tanggal->translatedFormat('D, d M'),
                'date' => $tanggal->toDateString(),
                'value' => (int) Transaction::query()
                    ->whereDate('transaction_date', $tanggal->toDateString())
                    ->sum('total'),
            ];
        });

        $transaksiTerbaru = Transaction::query()
            ->with('user')
            ->latest('transaction_date')
            ->latest('id')
            ->take(5)
            ->get();

        $stokMenipis = Product::query()
            ->where('stock', '<=', Product::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->take(5)
            ->get();
        $jumlahStokRendah = Product::query()
            ->where('stock', '<=', Product::LOW_STOCK_THRESHOLD)
            ->count();

        return view('dashboard', [
            'statistik' => [
                [
                    'label' => 'Total Produk',
                    'value' => $totalProduk,
                    'suffix' => 'Produk',
                    'icon' => 'box',
                    'tone' => 'primary',
                ],
                [
                    'label' => 'Stok Barang',
                    'value' => $totalStok,
                    'suffix' => 'Item',
                    'icon' => 'layers',
                    'tone' => 'success',
                ],
                [
                    'label' => 'Transaksi Hari Ini',
                    'value' => $transaksiHariIni,
                    'suffix' => 'Transaksi',
                    'icon' => 'cart',
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Pendapatan Hari Ini',
                    'value' => $pendapatanHariIni,
                    'prefix' => 'Rp',
                    'money' => true,
                    'suffix' => 'Total Penjualan',
                    'icon' => 'wallet',
                    'tone' => 'primary',
                ],
            ],
            'grafik' => $grafik,
            'transaksiTerbaru' => $transaksiTerbaru,
            'stokMenipis' => $stokMenipis,
            'jumlahStokRendah' => $jumlahStokRendah,
        ]);
    }
}