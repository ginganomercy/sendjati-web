<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Profile (Breeze) - Semua user login bisa akses profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Hanya Admin
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data
        Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
        Route::resource('items', ItemController::class)->except(['show', 'create', 'edit']);
        Route::get('/items/check-duplicate', [ItemController::class, 'checkDuplicate'])->name('items.check_duplicate');
        Route::get('/items/{item}/history', [ItemController::class, 'history'])->name('items.history');

        // Transaksi Core
        Route::resource('transactions', TransactionController::class)->only(['index', 'store']);

        // Audit Trail / Ledger
        Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
    });
});

require __DIR__.'/auth.php';

