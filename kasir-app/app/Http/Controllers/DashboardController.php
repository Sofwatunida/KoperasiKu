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
                    'label' => __('dashboard.stat_total_products'),
                    'value' => $totalProduk,
                    'suffix' => __('dashboard.stat_products_suffix'),
                    'icon' => 'box',
                    'tone' => 'primary',
                ],
                [
                    'label' => __('dashboard.stat_stock'),
                    'value' => $totalStok,
                    'suffix' => __('dashboard.stat_stock_suffix'),
                    'icon' => 'layers',
                    'tone' => 'success',
                ],
                [
                    'label' => __('dashboard.stat_today_transactions'),
                    'value' => $transaksiHariIni,
                    'suffix' => __('dashboard.stat_transactions_suffix'),
                    'icon' => 'cart',
                    'tone' => 'warning',
                ],
                [
                    'label' => __('dashboard.stat_today_revenue'),
                    'value' => $pendapatanHariIni,
                    'prefix' => 'Rp',
                    'money' => true,
                    'suffix' => __('dashboard.stat_revenue_suffix'),
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
