<?php

namespace App\Services;

use App\Contracts\EquipmentInventoryInterface;
use App\Models\Item;
use App\Models\InventoryLedger;
use Exception;

class EquipmentService implements EquipmentInventoryInterface
{
    /**
     * Menerapkan pola Event-Sourcing.
     * State 'current_stock' tidak diupdate langsung secara manual.
     * Ledger adalah source of truth.
     */
    public function recordIncomingItem(Item $item, int $quantity, string $notes, ?float $unitPrice = null, ?int $transactionId = null): void
    {
        if ($quantity <= 0) {
            throw new Exception("Kuantitas barang masuk harus positif.");
        }

        // Kalkulasi dinamis saldo akhir berdasarkan Ledger
        $currentAggregatedStock = $item->getAggregatedStockAttribute();
        $balanceAfter = $currentAggregatedStock + $quantity;

        InventoryLedger::create([
            'item_id'        => $item->id,
            'transaction_id' => $transactionId,
            'mutation_type'  => 'increment',
            'quantity'       => $quantity,
            'balance_after'  => $balanceAfter,
            'notes'          => $notes,
        ]);

        // Opsional: Perbarui harga satuan jika ada
        if ($unitPrice !== null) {
            $item->harga_satuan = $unitPrice;
            $item->save(); // Sengaja tidak menyentuh current_stock
        }
    }
}
