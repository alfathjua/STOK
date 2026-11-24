<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Stock records per tanggal untuk item ini.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Semua pengeluaran stok untuk item ini.
     */
    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class);
    }

    /**
     * Kode barang berbasis ID, e.g. KB-001, KB-042.
     */
    public function getItemCodeAttribute(): string
    {
        return 'KB-'.str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Computed total for this line.
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->qty * (float) $this->price;
    }
}
