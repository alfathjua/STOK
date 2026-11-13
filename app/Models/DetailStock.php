<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_in_id',
        'item_name',
        'description',
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
     * The parent stock-in record.
     */
    public function stockIn(): BelongsTo
    {
        return $this->belongsTo(StockIn::class);
    }

    /**
     * Computed total for this line.
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->qty * (float) $this->price;
    }
}
