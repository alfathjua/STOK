<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOut extends Model
{
    protected $fillable = [
        'stock_id',
        'detail_stock_id',
        'user_id',
        'company_name',
        'map_url',
        'qty',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Record stok gudang yang dipakai saat pengeluaran ini.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Detail item barang.
     */
    public function detailStock(): BelongsTo
    {
        return $this->belongsTo(DetailStock::class);
    }

    /**
     * User yang mencatat pengeluaran.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Total nilai pengeluaran ini.
     */
    public function getTotalValueAttribute(): float
    {
        return (float) $this->qty * (float) $this->price;
    }
}
