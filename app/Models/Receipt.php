<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'images',
    ];

    /**
     * The stock-in entry this receipt belongs to.
     */
    public function stockIn(): BelongsTo
    {
        return $this->belongsTo(StockIn::class, 'stock_id');
    }
}
