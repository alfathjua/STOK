<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make proof nullable — foto bukti bersifat opsional.
     */
    public function up(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->string('proof')->nullable()->change();
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->string('proof')->nullable(false)->change();
        });
    }
};
