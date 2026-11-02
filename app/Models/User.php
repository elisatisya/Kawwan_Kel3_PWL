<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public const STATUS_MENUNGGU = 'menunggu_verifikasi';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_NONAKTIF = 'nonaktif';

    protected $fillable = [
        'name',
        'email',
        'nik',
        'nomor_telepon',
        'alamat',
        'password',
        'role',
        'status',
        'catatan_verifikasi',
        'verified_at',
        'verified_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at' => 'datetime',
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

    /**
     * Kirim notifikasi pengaturan ulang kata sandi dengan template KAWWAN.
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
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

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'verified_by');
    }

    public function verifiedUsers(): HasMany
    {
        return $this->hasMany(self::class, 'verified_by');
    }

    public function isAktif(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    public function isMenungguVerifikasi(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    public function isDitolak(): bool
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    public function isNonaktif(): bool
    {
        return $this->status === self::STATUS_NONAKTIF;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'Menunggu Verifikasi',
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_NONAKTIF => 'Nonaktif',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU => 'status-badge-pending',
            self::STATUS_AKTIF => 'status-badge-active',
            self::STATUS_DITOLAK => 'status-badge-rejected',
            self::STATUS_NONAKTIF => 'status-badge-inactive',
            default => 'status-badge-default',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Admin Sistem',
            self::ROLE_DOKTER, 'dokter' => 'Dokter Hewan',
            self::ROLE_KADER => 'Kader Puskesmas',
            self::ROLE_PEMILIK, 'pemilik' => 'Pemilik Hewan',
            default => ucfirst(str_replace('_', ' ', (string) $this->role)),
        };
    }
}
