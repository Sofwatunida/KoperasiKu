<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ReportExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$dari, $sampai, $kasirId] = $this->periode($request);

        $transaksi = $this->query($dari, $sampai, $kasirId);

        $totalTransaksi = (clone $transaksi)->count();
        $totalPendapatan = (int) (clone $transaksi)->sum('total');
        $totalItemTerjual = (int) (clone $transaksi)->withSum('details as jumlah_item', 'quantity')->get()->sum('jumlah_item');

        $grafik = $this->grafikHarian($dari, $sampai, $kasirId);

        return view('reports.index', [
            'transaksi' => (clone $transaksi)->with(['details.product'])->latest('transaction_date')->latest('id')->get(),
            'dari' => $dari,
            'sampai' => $sampai,
            'kasirId' => $kasirId,
            'kasirList' => User::orderBy('name')->get(['id', 'name']),
            'totalTransaksi' => $totalTransaksi,
            'totalPendapatan' => $totalPendapatan,
            'totalItemTerjual' => $totalItemTerjual,
            'rataRataTransaksi' => $totalTransaksi > 0 ? (int) round($totalPendapatan / $totalTransaksi) : 0,
            'produkTerlaris' => $this->produkTerlaris($dari, $sampai, $kasirId),
            'grafik' => $grafik,
        ]);
    }

    public function print(Request $request): View
    {
        [$dari, $sampai, $kasirId] = $this->periode($request);

        $transaksi = $this->query($dari, $sampai, $kasirId);

        $totalTransaksi = (clone $transaksi)->count();
        $totalPendapatan = (int) (clone $transaksi)->sum('total');

        return view('reports.print', [
            'transaksi' => $transaksi->with(['details.product'])->latest('transaction_date')->latest('id')->get(),
            'dari' => $dari,
            'sampai' => $sampai,
            'kasirId' => $kasirId,
            'totalTransaksi' => $totalTransaksi,
            'totalPendapatan' => $totalPendapatan,
            'totalItemTerjual' => (int) (clone $transaksi)->withSum('details as jumlah_item', 'quantity')->get()->sum('jumlah_item'),
            'rataRataTransaksi' => $totalTransaksi > 0 ? (int) round($totalPendapatan / $totalTransaksi) : 0,
        ]);
    }

    /**
     * Unduh laporan sebagai berkas PDF.
     */
    public function exportPdf(Request $request, ReportExporter $exporter): Response
    {
        return $this->download($exporter->pdf($this->exportData($request)), 'pdf');
    }

    /**
     * Unduh laporan sebagai berkas Excel (.xlsx).
     */
    public function exportExcel(Request $request, ReportExporter $exporter): Response
    {
        return $this->download($exporter->xlsx($this->exportData($request)), 'xlsx');
    }

    /**
     * Unduh laporan sebagai berkas Word (.docx).
     */
    public function exportWord(Request $request, ReportExporter $exporter): Response
    {
        return $this->download($exporter->docx($this->exportData($request)), 'docx');
    }

    /**
     * Data mentah laporan (transaksi, produk, quantity, harga, subtotal) yang
     * dipakai bersama oleh seluruh format export.
     *
     * @return array<string, mixed>
     */
    private function exportData(Request $request): array
    {
        [$dari, $sampai, $kasirId] = $this->periode($request);

        $transaksi = $this->query($dari, $sampai, $kasirId)
            ->with(['details.product'])
            ->latest('transaction_date')
            ->latest('id')
            ->get();

        $rows = [];

        foreach ($transaksi as $transaction) {
            $tanggal = $transaction->date?->format('d/m/Y H:i') ?? '-';
            $kasir = $transaction->user?->name ?? __('report.unknown_cashier');

            if ($transaction->details->isEmpty()) {
                $rows[] = [$tanggal, $transaction->transaction_code, $kasir, '-', 0, 0, 0];

                continue;
            }

            foreach ($transaction->details as $detail) {
                $rows[] = [
                    $tanggal,
                    $transaction->transaction_code,
                    $kasir,
                    $detail->product?->name ?? __('report.deleted_product'),
                    (int) $detail->quantity,
                    (int) $detail->price,
                    (int) $detail->subtotal,
                ];
            }
        }

        return [
            'title' => __('report.title'),
            'period' => [
                'dari' => Carbon::parse($dari)->format('d/m/Y'),
                'sampai' => Carbon::parse($sampai)->format('d/m/Y'),
            ],
            'printed_at' => Carbon::now()->format('d/m/Y H:i'),
            'columns' => [
                __('report.columns.date'),
                __('report.columns.code'),
                __('report.columns.cashier'),
                __('report.columns.product'),
                __('report.columns.qty'),
                __('report.columns.price'),
                __('report.columns.subtotal'),
            ],
            'rows' => $rows,
            'summary' => [
                [__('report.summary.total_transactions'), (int) $transaksi->count()],
                [__('report.summary.total_revenue'), 'Rp'.number_format((int) $transaksi->sum('total'), 0, ',', '.')],
            ],
        ];
    }

    private function download(string $binary, string $extension): Response
    {
        $filename = 'laporan-penjualan-'.Carbon::now()->format('Ymd-His').'.'.$extension;

        return response($binary, 200, [
            'Content-Type' => $this->mimeFor($extension),
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) strlen($binary),
        ]);
    }

    private function mimeFor(string $extension): string
    {
        return match ($extension) {
            'pdf' => 'application/pdf',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };
    }

    private function query(string $dari, string $sampai, ?int $kasirId = null): Builder
    {
        return Transaction::query()
            ->with('user')
            ->when($kasirId, fn (Builder $query) => $query->where('user_id', $kasirId))
            ->betweenDates($dari, $sampai);
    }

    /**
     * Parse filter periode aktif plus filter kasir opsional.
     *
     * @return array{0: string, 1: string, 2: int|null}
     */
    private function periode(Request $request): array
    {
        $filters = $request->validate([
            'dari' => ['nullable', 'date_format:Y-m-d'],
            'sampai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:dari'],
            'kasir' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $kasirId = isset($filters['kasir']) ? (int) $filters['kasir'] : null;

        return [
            $filters['dari'] ?? Carbon::today()->startOfMonth()->toDateString(),
            $filters['sampai'] ?? Carbon::today()->toDateString(),
            $kasirId,
        ];
    }

    /**
     * Pendapatan per tanggal pada rentang yang dipilih.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function grafikHarian(string $dari, string $sampai, ?int $kasirId = null): Collection
    {
        $mulai = Carbon::parse($dari)->locale('id')->startOfDay();
        $akhir = Carbon::parse($sampai)->locale('id')->startOfDay();

        $pendapatanPerTanggal = $this->query($dari, $sampai, $kasirId)
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

    /**
     * Lima produk terlaris pada rentang yang dipilih.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function produkTerlaris(string $dari, string $sampai, ?int $kasirId = null): Collection
    {
        return Product::query()
            ->select('products.*')
            ->selectRaw('COALESCE(SUM(transaction_details.quantity), 0) as total_qty')
            ->join('transaction_details', 'products.id', '=', 'transaction_details.product_id')
            ->join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->whereBetween('transactions.transaction_date', [
                Carbon::parse($dari)->startOfDay(),
                Carbon::parse($sampai)->endOfDay(),
            ])
            ->when($kasirId, fn (Builder $query) => $query->where('transactions.user_id', $kasirId))
            ->groupBy('products.id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
    }
}
