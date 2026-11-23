<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'phone',
        'avatar',
        'role',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Semua log aktivitas yang dilakukan user ini.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    /**
     * Kunci-kunci foreman yang sudah pernah dipakai user ini saat register.
     */
    public function foremanKeys(): HasMany
    {
        return $this->hasMany(ForemanKey::class, 'used_by');
    }

    /**
     * Direktori penyimpanan foto profil berdasarkan role.
     */
    public function getAvatarDirectoryAttribute(): string
    {
        return $this->role === 'director' ? 'fp-director' : 'fp-foreman';
    }
}
