<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Contracts\EquipmentInventoryInterface;
use App\Contracts\ConsumableInventoryInterface;

class TransactionService
{
    public function __construct(
        protected EquipmentInventoryInterface $equipmentService,
        protected ConsumableInventoryInterface $consumableService
    ) {}

    public function processTransaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Buat Header Transaksi
            $transaction = Transaction::create([
                'reference_number' => 'TRX-' . strtoupper(uniqid()),
                'type'             => $data['type'],
                'user_id'          => auth()->id() ?? 1,
                'status'           => 'completed',
                'total_amount'     => 0,
            ]);

            $totalAmount = 0;

            // 2. Loop detail barang dan delegasikan ke Domain Service masing-masing
            foreach ($data['details'] as $detail) {
                // A. PESSIMISTIC LOCKING
                $item = Item::where('id', $detail['item_id'])->lockForUpdate()->firstOrFail();

                $unitPrice = $data['type'] === 'in' ? ($detail['unit_price'] ?? 0) : 0;
                $subtotal  = $unitPrice * $detail['quantity'];
                $totalAmount += $subtotal;

                $typeLabel = [
                    'in'     => 'Transaksi Masuk',
                    'out'    => 'Transaksi Keluar',
                    'return' => 'Retur Supplier',
                    'opname' => 'Stok Opname',
                ][$data['type']] ?? strtoupper($data['type']);

                // B. Pendelegasian ke Layer Abstraksi Spesifik (SOLID: ISP)
                if ($item->isEquipment()) {
                    if (in_array($data['type'], ['out', 'return'])) {
                        // Karena EquipmentInventoryInterface murni Append-Only, ia tidak punya recordOutgoingItem
                        throw new Exception("Integritas data ditolak: Inventaris Alat ({$item->name}) tidak dapat dikurangi (Append-Only).");
                    }
                    $this->equipmentService->recordIncomingItem($item, $detail['quantity'], $typeLabel, $unitPrice, $transaction->id);
                } else {
                    if (in_array($data['type'], ['out', 'return'])) {
                        $this->consumableService->recordOutgoingItem($item, $detail['quantity'], $typeLabel, $transaction->id);
                    } else {
                        $this->consumableService->recordIncomingItem($item, $detail['quantity'], $typeLabel, $unitPrice, $transaction->id);
                    }
                }

                // C. Simpan Detail Transaksi
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'item_id'        => $item->id,
                    'quantity'       => $detail['quantity'],
                    'unit_price'     => $unitPrice,
                    'subtotal'       => $subtotal,
                ]);
            }

            // Update total amount header transaksi
            if ($totalAmount > 0) {
                $transaction->total_amount = $totalAmount;
                $transaction->save();
            }

            return $transaction;
        }, 3);
    }
}
