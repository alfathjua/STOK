<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Siapa yang melakukan aksi (nullable: system/guest edge-case)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');

            // Jenis aksi: create | edit | delete
            $table->enum('action', ['create', 'edit', 'delete']);

            // Fitur tempat aksi terjadi, e.g. "Stok Masuk", "Stok Keluar"
            $table->string('feature');

            // Deskripsi singkat aksi, e.g. "Menambahkan 3 item dari Toko ABC"
            $table->text('description');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
