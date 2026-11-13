<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'stock_origin',
        'proof',
    ];

    /**
     * Use UUID as the route model binding key instead of the numeric id.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Boot: auto-generate UUID on create.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (StockIn $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * The user (foreman) who recorded this stock-in.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Individual item lines for this stock-in.
     */
    public function details(): HasMany
    {
        return $this->hasMany(DetailStock::class);
    }

    /**
     * Purchase receipt images.
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class, 'stock_id');
    }

    /**
     * Grand total value of all items.
     */
    public function getTotalAttribute(): float
    {
        return $this->details->sum(fn (DetailStock $d) => $d->qty * $d->price);
    }
}
