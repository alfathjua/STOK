<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'detail_stock_id',
        'date',
        'qty_in',
        'qty_remaining',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'qty_in' => 'integer',
            'qty_remaining' => 'integer',
        ];
    }

    /**
     * The detail stock (item line) this stock record belongs to.
     */
    public function detailStock(): BelongsTo
    {
        return $this->belongsTo(DetailStock::class);
    }

    /**
     * Whether there is remaining stock (> 0).
     */
    public function getHasRemainingStockAttribute(): bool
    {
        return $this->qty_remaining > 0;
    }
}
