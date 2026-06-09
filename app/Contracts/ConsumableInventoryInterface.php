<?php

namespace App\Contracts;

use App\Models\Item;

/**
 * Interface Segregation Principle (ISP)
 * Kontrak khusus untuk Bahan Baku. 
 * Memiliki fungsi penambahan dan pengurangan stok.
 */
interface ConsumableInventoryInterface
{
    public function recordIncomingItem(Item $item, int $quantity, string $notes, ?float $unitPrice = null, ?int $transactionId = null): void;
    public function recordOutgoingItem(Item $item, int $quantity, string $notes, ?int $transactionId = null): void;
}
