<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogger
{
    /**
     * Catat aksi 'create' oleh user yang sedang login.
     */
    public static function create(string $feature, string $description): void
    {
        self::log('create', $feature, $description);
    }

    /**
     * Catat aksi 'edit' oleh user yang sedang login.
     */
    public static function edit(string $feature, string $description): void
    {
        self::log('edit', $feature, $description);
    }

    /**
     * Catat aksi 'delete' oleh user yang sedang login.
     */
    public static function delete(string $feature, string $description): void
    {
        self::log('delete', $feature, $description);
    }

    /**
     * Tulis record ke tabel activity_logs.
     */
    private static function log(string $action, string $feature, string $description): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'feature' => $feature,
            'description' => $description,
        ]);
    }
}
