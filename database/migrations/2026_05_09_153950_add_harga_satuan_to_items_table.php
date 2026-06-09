<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Harga Pokok Pembelian per satuan — dasar kalkulasi nilai stok
            // Nullable karena data lama tidak memiliki harga, default 0
            $table->decimal('harga_satuan', 15, 2)->default(0)->after('minimum_stock');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('harga_satuan');
        });
    }
};
