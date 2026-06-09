<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Services\TransactionService;
use Exception;

class EquipmentTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $equipment;
    protected $transactionService;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
        // Setup kebutuhan relasi
        $category = Category::create(['name' => 'Peralatan', 'type' => 'raw_material']);
        $unit = Unit::create(['name' => 'Pcs']);
        
        $this->equipment = Item::create([
            'sku' => 'EQP-TEST',
            'name' => 'Mesin Espresso Test',
            'type' => 'equipment',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'minimum_stock' => 1,
            'current_stock' => 0,
            'harga_satuan' => 10000000,
        ]);
        
        $this->transactionService = app(TransactionService::class);
    }

    public function test_equipment_can_be_added_via_in_transaction()
    {
        $data = [
            'type' => 'in',
            'details' => [
                [
                    'item_id' => $this->equipment->id,
                    'quantity' => 2,
                    'unit_price' => 10000000
                ]
            ]
        ];

        $this->transactionService->processTransaction($data);

        // Verifikasi stok (Event Sourcing: 0 + 2 = 2)
        $this->assertEquals(2, $this->equipment->fresh()->current_stock);
        
        // Verifikasi histori ledger tercetak
        $this->assertDatabaseHas('inventory_ledgers', [
            'item_id' => $this->equipment->id,
            'mutation_type' => 'increment',
            'quantity' => 2,
            'balance_after' => 2,
        ]);
    }

    public function test_equipment_cannot_be_reduced_via_out_transaction()
    {
        // Mengharapkan Exception spesifik dilempar
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Integritas data ditolak: Inventaris Alat (Mesin Espresso Test) tidak dapat dikurangi (Append-Only).');

        $data = [
            'type' => 'out',
            'details' => [
                [
                    'item_id' => $this->equipment->id,
                    'quantity' => 1,
                    'unit_price' => 0
                ]
            ]
        ];

        $this->transactionService->processTransaction($data);
    }
}
