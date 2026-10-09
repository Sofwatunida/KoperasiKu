<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$dari, $sampai] = $this->periode($request);

        $transaksi = $this->query($dari, $sampai);

        $totalTransaksi = (clone $transaksi)->count();
        $totalPendapatan = (int) (clone $transaksi)->sum('total');

        $grafik = $this->grafikHarian($dari, $sampai);

        return view('reports.index', [
            'transaksi' => (clone $transaksi)->latest('transaction_date')->latest('id')->get(),
            'dari' => $dari,
            'sampai' => $sampai,
            'totalTransaksi' => $totalTransaksi,
            'totalPendapatan' => $totalPendapatan,
            'grafik' => $grafik,
        ]);
    }

    public function print(Request $request): View
    {
        [$dari, $sampai] = $this->periode($request);

        $transaksi = $this->query($dari, $sampai);

        return view('reports.print', [
            'transaksi' => $transaksi->latest('transaction_date')->latest('id')->get(),
            'dari' => $dari,
            'sampai' => $sampai,
            'totalTransaksi' => (clone $transaksi)->count(),
            'totalPendapatan' => (int) (clone $transaksi)->sum('total'),
        ]);
    }

    private function query(string $dari, string $sampai): \Illuminate\Database\Eloquent\Builder
    {
        return Transaction::query()
            ->with('user')
            ->betweenDates($dari, $sampai);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function periode(Request $request): array
    {
        $filters = $request->validate([
            'dari' => ['nullable', 'date_format:Y-m-d'],
            'sampai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:dari'],
        ]);

        return [
            $filters['dari'] ?? Carbon::today()->startOfMonth()->toDateString(),
            $filters['sampai'] ?? Carbon::today()->toDateString(),
        ];
    }

    /**
     * Pendapatan per tanggal pada rentang yang dipilih.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function grafikHarian(string $dari, string $sampai): \Illuminate\Support\Collection
    {
        $mulai = Carbon::parse($dari)->locale('id')->startOfDay();
        $akhir = Carbon::parse($sampai)->locale('id')->startOfDay();

        $pendapatanPerTanggal = $this->query($dari, $sampai)
            ->get()
            ->groupBy(fn (Transaction $trx) => $trx->date->toDateString())
            ->map(fn ($group) => (int) $group->sum('total'));

        $jumlahHari = min($mulai->diffInDays($akhir) + 1, 31);

        return collect(range(0, max($jumlahHari - 1, 0)))
            ->map(function (int $index) use ($mulai, $pendapatanPerTanggal) {
                $tanggal = $mulai->copy()->addDays($index);

                return [
                    'label' => $tanggal->translatedFormat('d M'),
                    'value' => $pendapatanPerTanggal->get($tanggal->toDateString(), 0),
                ];
            });
    }
}