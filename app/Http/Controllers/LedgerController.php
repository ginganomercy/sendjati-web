<?php

namespace App\Http\Controllers;

use App\Models\InventoryLedger;
use Inertia\Inertia;

class LedgerController extends Controller
{
    public function index()
    {
        $ledgers = InventoryLedger::with(['item', 'transaction'])
                    ->latest()
                    ->paginate(20);
                    
        return Inertia::render('Ledger/Index', [
            'ledgers' => $ledgers
        ]);
    }
}
