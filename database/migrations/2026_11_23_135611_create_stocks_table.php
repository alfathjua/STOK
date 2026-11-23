<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // Referensi ke detail baris stok masuk
            $table->foreignId('detail_stock_id')
                ->constrained('detail_stocks')
                ->onDelete('cascade');

            // Tanggal stok (default: tanggal pencatatan stok masuk)
            $table->date('date');

            // Qty masuk dari stok masuk (mirror dari detail_stocks.qty)
            $table->integer('qty_in')->default(0);

            // Sisa stok yang bisa diedit user
            $table->integer('qty_remaining')->default(0);

            $table->timestamps();

            // Satu item hanya boleh punya satu record per tanggal
            $table->unique(['detail_stock_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
