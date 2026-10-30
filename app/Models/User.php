<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_DOKTER = 'dokter_hewan';

    public const ROLE_KADER = 'kader';

    public const ROLE_PEMILIK = 'pemilik_hewan';

    protected $fillable = [
        'name',
        'email',
        'nik',
        'nomor_telepon',
        'alamat',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['role'] = match ($value) {
            'dokter' => self::ROLE_DOKTER,
            'pemilik' => self::ROLE_PEMILIK,
            default => $value,
        };
    }

    public function hewan(): HasMany
    {
        return $this->hasMany(Hewan::class, 'user_id');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'user_id');
    }

    public function artikel(): HasMany
    {
        return $this->hasMany(Artikel::class, 'user_id');
    }

    public function hasRole(string $role): bool
    {
        if ($role === 'dokter' || $role === 'dokter_hewan') {
            return $this->isDokter();
        }

        if ($role === 'pemilik' || $role === 'pemilik_hewan') {
            return $this->isPemilik();
        }

        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isDokter(): bool
    {
        return in_array($this->role, [self::ROLE_DOKTER, 'dokter'], true);
    }

    public function isKader(): bool
    {
        return $this->role === self::ROLE_KADER;
    }

    public function isPemilik(): bool
    {
        return in_array($this->role, [self::ROLE_PEMILIK, 'pemilik'], true);
    }
}
