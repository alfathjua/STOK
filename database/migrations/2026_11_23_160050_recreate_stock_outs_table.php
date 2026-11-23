<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop tabel lama yang strukturnya tidak lengkap, lalu buat ulang
     * dengan kolom yang sesuai kebutuhan fitur stok keluar.
     */
    public function up(): void
    {
        Schema::dropIfExists('stock_outs');

        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();

            // Referensi ke record stocks (stok gudang per tanggal)
            $table->foreignId('stock_id')
                ->constrained('stocks')
                ->onDelete('cascade');

            // Referensi ke detail baris barang
            $table->foreignId('detail_stock_id')
                ->constrained('detail_stocks')
                ->onDelete('cascade');

            // Siapa yang mencatat
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Penerima barang
            $table->string('company_name');
            $table->string('map_url')->nullable();

            // Jumlah dan harga satuan saat keluar (snapshot)
            $table->integer('qty');
            $table->decimal('price', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_outs');

        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->string('map');
            $table->timestamps();
        });
    }
};
