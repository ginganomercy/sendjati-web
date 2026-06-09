<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\InventoryLedger;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Class DashboardController
 * 
 * Mengendalikan logika dan pengumpulan data metrik untuk halaman utama (Dashboard).
 * Berperan penting dalam memvisualisasikan kesehatan inventaris secara real-time
 * melalui agregasi data dari berbagai tabel (Item, Transaction, InventoryLedger).
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard dengan data analitik terpadu.
     * 
     * Proses pengumpulan data meliputi:
     * 1. Metrik Barang: Total barang, stok kritis, dan stok habis.
     * 2. Valuasi Aset: Menghitung nilai uang dari seluruh stok berdasarkan `harga_satuan`.
     * 3. Metrik Transaksi: Menghitung total mutasi (Masuk/Keluar) pada bulan berjalan.
     * 4. Time-Series Chart: Menyusun data agregat 6 bulan terakhir dari tabel Ledger.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $now   = now();
        $bulan = $now->month;
        $tahun = $now->year;

        // ── Query Dasar Item ──────────────────────────────────────────────
        $itemQuery = Item::query();
        if ($request->filled('department')) {
            $itemQuery->where('department', $request->department);
        }

        // ── Metrik Inventaris ──────────────────────────────────────────────
        $totalBarang = (clone $itemQuery)->count();
        $stokKritis = (clone $itemQuery)->whereRaw('current_stock <= minimum_stock')->count();
        $stokHabis = (clone $itemQuery)->where('current_stock', 0)->count();
        $nilaiStok = (clone $itemQuery)->where('harga_satuan', '>', 0)
            ->selectRaw('SUM(current_stock * harga_satuan) as total')
            ->value('total') ?? 0;

        // ── Metrik Transaksi Bulan Ini ─────────────────────────────────────
        $txMasukQuery = Transaction::where('type', 'in')->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun);
        $txKeluarQuery = Transaction::where('type', 'out')->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun);
        $txAllQuery = Transaction::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun);

        if ($request->filled('department')) {
            $txMasukQuery->whereHas('details.item', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
            $txKeluarQuery->whereHas('details.item', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
            $txAllQuery->whereHas('details.item', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $transaksiMasukBulanIni = $txMasukQuery->count();
        $transaksiKeluarBulanIni = $txKeluarQuery->count();
        $totalTransaksiBulanIni = $txAllQuery->count();

        // ── Grafik: Mutasi stok 6 bulan terakhir ──────────────────────────
        $chartData = collect(range(5, 0))->map(function ($bulanLalu) use ($now, $request) {
            $target = $now->copy()->subMonths($bulanLalu);
            
            $ledgerQuery = InventoryLedger::whereMonth('created_at', $target->month)
                ->whereYear('created_at', $target->year);

            if ($request->filled('department')) {
                $ledgerQuery->whereHas('item', function($q) use ($request) {
                    $q->where('department', $request->department);
                });
            }

            $masuk  = (clone $ledgerQuery)->where('mutation_type', 'increment')->sum('quantity');
            $keluar = (clone $ledgerQuery)->where('mutation_type', 'decrement')->sum('quantity');

            return [
                'label'  => $target->translatedFormat('M Y'),
                'masuk'  => (int) $masuk,
                'keluar' => (int) $keluar,
            ];
        })->values();

        // ── Daftar barang stok kritis (untuk alert panel) ─────────────────
        $barangKritis = (clone $itemQuery)->with('unit')
            ->whereRaw('current_stock <= minimum_stock')
            ->orderBy('current_stock')
            ->limit(5)
            ->get(['id', 'name', 'sku', 'current_stock', 'minimum_stock', 'unit_id']);

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'totalBarang'             => $totalBarang,
                'stokKritis'              => $stokKritis,
                'stokHabis'               => $stokHabis,
                'nilaiStok'               => (float) $nilaiStok,
                'transaksiMasukBulanIni'  => $transaksiMasukBulanIni,
                'transaksiKeluarBulanIni' => $transaksiKeluarBulanIni,
                'totalTransaksiBulanIni'  => $totalTransaksiBulanIni,
                'bulanIni'                => $now->translatedFormat('F Y'),
            ],
            'chartData'    => $chartData,
            'barangKritis' => $barangKritis,
            'filters'      => $request->only(['department']),
        ]);
    }
}
