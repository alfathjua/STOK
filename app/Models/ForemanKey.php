<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ForemanKey extends Model
{
    protected $fillable = [
        'key',
        'is_used',
        'used_by',
    ];

    protected function casts(): array
    {
        return [
            'is_used' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * User foreman yang menggunakan kunci ini.
     */
    public function usedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Apakah kunci ini sudah kedaluwarsa (> 24 jam sejak dibuat).
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->created_at->lt(Carbon::now()->subHours(24));
    }

    /**
     * Apakah kunci ini masih valid (belum dipakai DAN belum kedaluwarsa).
     */
    public function getIsValidAttribute(): bool
    {
        return ! $this->is_used && ! $this->is_expired;
    }

    /**
     * Sisa waktu kedaluwarsa dalam format ringkas, e.g. "23 jam 15 menit".
     */
    public function getExpiresInAttribute(): string
    {
        $expiresAt = $this->created_at->addHours(24);
        $diff = Carbon::now()->diff($expiresAt);

        if ($this->is_expired) {
            return 'Kedaluwarsa';
        }

        $parts = [];
        if ($diff->h > 0) {
            $parts[] = $diff->h.' jam';
        }
        if ($diff->i > 0) {
            $parts[] = $diff->i.' menit';
        }

        return implode(' ', $parts) ?: '< 1 menit';
    }
}
