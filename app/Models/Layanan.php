<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Layanan extends Model
{
    protected $table = 'layanan';

    protected $fillable = [
        'nama',
        'deskripsi',
        'biaya',
        'durasi_menit',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'biaya' => 'integer',
            'durasi_menit' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function pemesananLayanan(): HasMany
    {
        return $this->hasMany(PemesananLayanan::class);
    }

    public function dokter(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'dokter_layanan',
            'layanan_id',
            'dokter_id'
        )->withTimestamps();
    }
}
