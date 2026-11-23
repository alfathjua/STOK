<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'feature',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * User yang melakukan aksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label warna berdasarkan jenis aksi.
     * Digunakan di view untuk menentukan kelas CSS border/badge.
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'create' => 'green',
            'edit' => 'blue',
            'delete' => 'red',
            default => 'slate',
        };
    }

    /**
     * Label teks aksi dalam Bahasa Indonesia.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'create' => 'Tambah',
            'edit' => 'Edit',
            'delete' => 'Hapus',
            default => $this->action,
        };
    }
}
