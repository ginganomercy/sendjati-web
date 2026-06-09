<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\InventoryLedger;
use App\Models\Unit;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Class ItemController
 * 
 * Bertanggung jawab penuh terhadap operasi CRUD Master Data Barang (Inventory).
 * Selain itu, controller ini juga menyediakan endpoint API JSON untuk menampilkan
 * riwayat mutasi (Ledger) per item secara spesifik di frontend modal.
 */
class ItemController extends Controller
{
    public function __construct(protected ItemService $itemService)
    {
    }

    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        $categoriesQuery = Category::select('id', 'name');
        if ($request->filled('department')) {
            $categoriesQuery->where('department', $request->department);
        }

        return Inertia::render('MasterData/Items/Index', [
            'items'      => $items,
            'categories' => $categoriesQuery->get(),
            'units'      => Unit::all(['id', 'name']),
            'filters'    => $request->only(['search', 'category_id', 'department', 'type']),
        ]);
    }

    public function checkDuplicate(Request $request)
    {
        $item = Item::where('name', $request->name)->first();
        if ($item) {
            return response()->json(['exists' => true, 'item' => $item]);
        }
        return response()->json(['exists' => false]);
    }

    /**
     * Mengembalikan riwayat mutasi stok untuk satu item tertentu.
     * Digunakan oleh modal Timeline Histori di frontend Vue.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Item $item)
    {
        // Map teks lama (teknis) ke bahasa Indonesia yang ramah pengguna
        // Berlaku untuk data lama maupun baru — tanpa perlu migrasi database
        $notesMap = [
            'Otomatis via Mutasi IN'     => 'Transaksi Masuk',
            'Otomatis via Mutasi OUT'    => 'Transaksi Keluar',
            'Otomatis via Mutasi RETURN' => 'Retur Supplier',
            'Otomatis via Mutasi OPNAME' => 'Stok Opname',
        ];

        // Map tipe transaksi ke label bahasa Indonesia
        $typeLabel = [
            'in'     => 'Transaksi Masuk',
            'out'    => 'Transaksi Keluar',
            'return' => 'Retur Supplier',
            'opname' => 'Stok Opname',
        ];

        $ledgers = InventoryLedger::where('item_id', $item->id)
            ->with('transaction:id,reference_number,type')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($l) use ($notesMap, $typeLabel) {
                // Normalisasi teks keterangan
                $notes = $notesMap[$l->notes] ?? $l->notes;

                // Referensi yang bermakna: gunakan label tipe, bukan kode acak
                $trxType = $l->transaction?->type;
                $reference = $trxType
                    ? ($typeLabel[$trxType] ?? $l->transaction->reference_number)
                    : null;

                return [
                    'id'            => $l->id,
                    'date'          => $l->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i'),
                    'mutation_type' => $l->mutation_type,
                    'quantity'      => $l->quantity,
                    'balance_after' => $l->balance_after,
                    'notes'         => $notes,
                    'reference'     => $reference,
                ];
            });

        return response()->json([
            'item'    => ['id' => $item->id, 'name' => $item->name, 'sku' => $item->sku],
            'ledgers' => $ledgers,
        ]);
    }

    public function store(StoreItemRequest $request)
    {
        $this->itemService->createItem($request->validated());
        return redirect()->back()->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $this->itemService->updateItem($item, $request->validated());
        return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        // Soft delete — data terhapus dari tampilan tapi tetap tersimpan di DB untuk audit
        $this->itemService->deleteItem($item);
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }
}
