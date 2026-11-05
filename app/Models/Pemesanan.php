<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';

    protected $fillable = [
        'user_id',
        'hewan_id',
        'jadwal_pelayanan_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hewan(): BelongsTo
    {
        return $this->belongsTo(Hewan::class);
    }

    public function jadwalPelayanan(): BelongsTo
    {
        return $this->belongsTo(JadwalPelayanan::class);
    }

    public function pemeriksaan(): HasOne
    {
        return $this->hasOne(Pemeriksaan::class);
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class);
    }

    // Detail layanan dalam satu pemesanan
    public function pemesananLayanan(): HasMany
    {
        return $this->hasMany(PemesananLayanan::class);
    }
    // Antrean untuk setiap dokter dalam pemesanan
    public function antrean(): HasMany
    {
        return $this->hasMany(Antrean::class);
    }
}

