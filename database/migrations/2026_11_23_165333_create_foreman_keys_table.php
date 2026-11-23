<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foreman_keys', function (Blueprint $table) {
            $table->id();

            // Kode 6 karakter (huruf+angka) yang dibuat director
            $table->string('key', 6)->unique();

            // Kapan kunci ini dibuat (expired = created_at + 24 jam)
            $table->boolean('is_used')->default(false);

            // User foreman yang memakai kunci ini saat register (nullable sampai dipakai)
            $table->foreignId('used_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foreman_keys');
    }
};
