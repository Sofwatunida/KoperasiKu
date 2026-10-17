<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
        [$dari, $sampai] = $this->periode($request);

        $transaksi = $this->query($dari, $sampai)
            ->with(['details.product'])
            ->latest('transaction_date')
            ->latest('id')
            ->get();

        $rows = [];

        foreach ($transaksi as $transaction) {
            $tanggal = $transaction->date?->format('d/m/Y H:i') ?? '-';
            $kasir = $transaction->user?->name ?? 'Petugas';

            if ($transaction->details->isEmpty()) {
                $rows[] = [$tanggal, $transaction->transaction_code, $kasir, '-', 0, 0, 0];

                continue;
            }

            foreach ($transaction->details as $detail) {
                $rows[] = [
                    $tanggal,
                    $transaction->transaction_code,
                    $kasir,
                    $detail->product?->name ?? 'Produk dihapus',
                    (int) $detail->quantity,
                    (int) $detail->price,
                    (int) $detail->subtotal,
                ];
            }
        }

        return [
            'title' => 'Laporan Penjualan',
            'period' => [
                'dari' => Carbon::parse($dari)->format('d/m/Y'),
                'sampai' => Carbon::parse($sampai)->format('d/m/Y'),
            ],
            'printed_at' => Carbon::now()->format('d/m/Y H:i'),
            'columns' => ['Tanggal', 'Kode', 'Kasir', 'Produk', 'Qty', 'Harga', 'Subtotal'],
            'rows' => $rows,
            'summary' => [
                ['Total Transaksi', (int) $transaksi->count()],
                ['Total Pendapatan', 'Rp'.number_format((int) $transaksi->sum('total'), 0, ',', '.')],
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

    private function query(string $dari, string $sampai): Builder
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
     * @return Collection<int, array<string, mixed>>
     */
    private function grafikHarian(string $dari, string $sampai): Collection
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
