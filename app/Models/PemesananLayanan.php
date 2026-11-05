<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemesananLayanan extends Model
{
    protected $table = 'pemesanan_layanan';

    protected $fillable = [
        'pemesanan_id',
        'layanan_id',
        'dokter_id',
        'biaya',
    ];

    protected function casts(): array
    {
        return [
            'biaya' => 'integer',
        ];
    }

    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }
}
