<?php

namespace App\Services;

use App\Contracts\ConsumableInventoryInterface;
use App\Models\Item;
use App\Models\InventoryLedger;
use Exception;

class ConsumableService implements ConsumableInventoryInterface
{
    public function recordIncomingItem(Item $item, int $quantity, string $notes, ?float $unitPrice = null, ?int $transactionId = null): void
    {
        if ($quantity <= 0) {
            throw new Exception("Kuantitas barang masuk harus positif.");
        }

        $item->current_stock += $quantity;
        if ($unitPrice !== null) {
            $item->harga_satuan = $unitPrice;
        }
        $item->save();

        InventoryLedger::create([
            'item_id'        => $item->id,
            'transaction_id' => $transactionId,
            'mutation_type'  => 'increment',
            'quantity'       => $quantity,
            'balance_after'  => $item->current_stock,
            'notes'          => $notes,
        ]);
    }

    public function recordOutgoingItem(Item $item, int $quantity, string $notes, ?int $transactionId = null): void
    {
        if ($quantity <= 0) {
            throw new Exception("Kuantitas barang keluar harus positif.");
        }

        if ($item->current_stock < $quantity) {
            throw new Exception("Stok tidak mencukupi untuk item: {$item->name}. Sisa stok: {$item->current_stock}");
        }

        $item->current_stock -= $quantity;
        $item->save();

        InventoryLedger::create([
            'item_id'        => $item->id,
            'transaction_id' => $transactionId,
            'mutation_type'  => 'decrement',
            'quantity'       => $quantity,
            'balance_after'  => $item->current_stock,
            'notes'          => $notes,
        ]);
    }
}
