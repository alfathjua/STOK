<?php

use App\Models\StockIn;
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
        Schema::table('detail_stocks', function (Blueprint $table) {
            $table->foreignId('stock_in_id')
                ->after('id')
                ->constrained('stock_ins')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_stocks', function (Blueprint $table) {
            $table->dropForeignIdFor(StockIn::class);
            $table->dropColumn('stock_in_id');
        });
    }
};
