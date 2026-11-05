<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPelayanan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelayanan';

    protected $fillable = [
        'dokter_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'kuota',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    // Relasi dengan pemesanan
    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class);
    }

    // Relasi dengan dokter
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    // Scope jadwal tersedia
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }
}
