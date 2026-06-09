<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku', 
        'name', 
        'category_id', 
        'unit_id', 
        'minimum_stock', 
        'current_stock',
        'harga_satuan',
        'type',
        'department',
    ];

    protected $casts = [
        'harga_satuan' => 'float',
    ];

    public const TYPE_CONSUMABLE = 'consumable';
    public const TYPE_EQUIPMENT = 'equipment';

    public const DEPARTMENT_KITCHEN = 'kitchen';
    public const DEPARTMENT_BAR = 'bar';

    protected static function booted()
    {
        static::creating(function (Item $item) {
            if (empty($item->sku)) {
                $deptCode = $item->department === self::DEPARTMENT_KITCHEN ? 'KTC' : 'BAR';
                $typeCode = $item->type === self::TYPE_EQUIPMENT ? 'EQP' : 'RAW';
                
                $lastItem = self::where('department', $item->department)
                                ->where('type', $item->type)
                                ->withTrashed()
                                ->orderBy('id', 'desc')
                                ->first();
                
                $nextNumber = 1;
                if ($lastItem && preg_match('/-(\d+)$/', $lastItem->sku, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                }
                
                $item->sku = sprintf("%s-%s-%03d", $deptCode, $typeCode, $nextNumber);
            }
        });
    }

    public function isEquipment(): bool
    {
        return $this->type === self::TYPE_EQUIPMENT;
    }

    public function isConsumable(): bool
    {
        return $this->type === self::TYPE_CONSUMABLE;
    }

    public function isKitchen(): bool
    {
        return $this->department === self::DEPARTMENT_KITCHEN;
    }

    public function isBar(): bool
    {
        return $this->department === self::DEPARTMENT_BAR;
    }

    // Accessor Mutator untuk current_stock
    public function getCurrentStockAttribute($value)
    {
        if ($this->isEquipment()) {
            return $this->getAggregatedStockAttribute();
        }
        return $value;
    }

    // Kalkulasi mutlak Event-Sourcing (Ledger = Source of Truth)
    public function getAggregatedStockAttribute(): int
    {
        $increment = InventoryLedger::where('item_id', $this->id)
            ->where('mutation_type', 'increment')
            ->sum('quantity');
        
        $decrement = InventoryLedger::where('item_id', $this->id)
            ->where('mutation_type', 'decrement')
            ->sum('quantity');
        
        return $increment - $decrement;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
