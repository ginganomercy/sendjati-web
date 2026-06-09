<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Services\TransactionService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Class TransactionController
 * 
 * Mengatur jalannya alur transaksi stok (Barang Masuk, Keluar, Retur, Opname).
 * Logika database dipisahkan dan didelegasikan ke TransactionService agar
 * controller ini tetap ramping dan fokus pada HTTP routing.
 */
class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService)
    {
    }

    public function index(Request $request)
    {
        $query = \App\Models\Transaction::with(['details.item'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('department')) {
            $query->whereHas('details.item', function($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $transactions = $query->paginate(15)->withQueryString();

        $itemsQuery = \App\Models\Item::with('unit');
        if ($request->filled('department')) {
            $itemsQuery->where('department', $request->department);
        }

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'items'        => $itemsQuery->get(),
            'filters'      => $request->only(['type', 'department']),
        ]);
    }

    /**
     * Memvalidasi dan menyimpan transaksi baru.
     * Menggunakan blok try-catch untuk menangani Exception dari TransactionService
     * (misal: stok tidak cukup untuk Transaksi Keluar).
     */
    public function store(StoreTransactionRequest $request)
    {
        try {
            $this->transactionService->processTransaction($request->validated());
            return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil diproses.');
        } catch (Exception $e) {
            // Menangkap error stok tidak cukup atau database error
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
