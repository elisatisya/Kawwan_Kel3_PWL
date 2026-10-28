<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalPelayanan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelayanan';

    protected $fillable = [
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

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }
}
