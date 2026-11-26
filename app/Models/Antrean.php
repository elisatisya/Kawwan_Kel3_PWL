<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Antrean extends Model
{
    protected $table = 'antrean';

    protected $fillable = [
        'pemesanan_id',
        'dokter_id',
        'tanggal',
        'nomor_antrean',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    /**
     * Menghasilkan nomor antrean unik per dokter per tanggal.
     * Format: D{dokter_id}-{urutan 3 digit}, misal D3-001, D7-001.
     */
    public static function buatNomorBaru(int $dokterId, string $tanggal): string
    {
        $nomorTerakhir = self::where('dokter_id', $dokterId)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->map(function ($item) {
                $parts = explode('-', (string) $item->nomor_antrean);

                return (int) preg_replace('/\D/', '', (string) end($parts));
            })
            ->max() ?? 0;

        return 'D'.$dokterId.'-'.str_pad(
            (string) ($nomorTerakhir + 1),
            3,
            '0',
            STR_PAD_LEFT
        );
    }
}
