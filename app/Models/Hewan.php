<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hewan extends Model
{
    use HasFactory;

    protected $table = 'hewan';

    protected $fillable = [
        'user_id',
        'nama',
        'jenis',
        'ras',
        'jenis_kelamin',
        'tanggal_lahir',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pemesanan(): HasMany
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function getUmurAttribute(): ?string
    {
        if (! $this->tanggal_lahir) {
            return null;
        }

        $years = (int) $this->tanggal_lahir->diffInYears(now());
        $months = (int) $this->tanggal_lahir->diffInMonths(now()) % 12;

        if ($years > 0) {
            return $years . ' tahun' . ($months > 0 ? ' ' . $months . ' bulan' : '');
        }

        return max(1, $months) . ' bulan';
    }
}
