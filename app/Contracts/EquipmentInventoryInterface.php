<?php

namespace App\Contracts;

use App\Models\Item;

/**
 * Interface Segregation Principle (ISP)
 * Kontrak khusus untuk Aset Tetap/Peralatan. 
 * Tidak memiliki fungsi pengurangan (recordOutgoingItem).
 */
interface EquipmentInventoryInterface
{
    public function recordIncomingItem(Item $item, int $quantity, string $notes, ?float $unitPrice = null, ?int $transactionId = null): void;
}
