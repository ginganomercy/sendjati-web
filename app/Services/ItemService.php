<?php

namespace App\Services;

use App\Models\InventoryLedger;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class ItemService
{
    /**
     * Membuat entitas Item baru.
     * Jika stok awal > 0, otomatis merekam entri pertama ke inventory_ledgers
     * sebagai "Stok Awal Barang" sehingga riwayat barang dimulai dari tanggal input.
     */
    public function createItem(array $data): Item
    {
        return DB::transaction(function () use ($data) {
            $item = Item::create($data);

            // Rekam ke Buku Besar jika ada stok awal
            if (($data['current_stock'] ?? 0) > 0) {
                InventoryLedger::create([
                    'item_id'        => $item->id,
                    'transaction_id' => null, // Bukan dari transaksi, ini entry awal
                    'mutation_type'  => 'increment',
                    'quantity'       => $data['current_stock'],
                    'balance_after'  => $data['current_stock'],
                    'notes'          => 'Stok Awal Barang',
                ]);
            }

            return $item;
        });
    }

    /**
     * Memperbarui entitas Item.
     * Perubahan stok dilakukan via Transaksi, bukan via Edit Barang — sehingga tidak ada
     * rekaman ledger di sini (mencegah manipulasi stok tanpa jejak).
     */
    public function updateItem(Item $item, array $data): bool
    {
        return $item->update($data);
    }

    /**
     * Melakukan Soft Delete pada Item.
     */
    public function deleteItem(Item $item): bool
    {
        return $item->delete();
    }
}
