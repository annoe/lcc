<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    use HasUlids;

    protected $table = 'provinsis';

    /**
     * 'id' disertakan agar importSave bisa menyuntikkan ULID yang
     * sudah digenerate di preview (mencegah ID berubah saat save).
     */
    protected $fillable = [
        'id',
        'kode',
        'nama',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Scopes ──────────────────────────────────────────

    /** Pencarian bebas pada kode atau nama */
    public function scopeSearch($query, string $keyword): void
    {
        $query->where(function ($q) use ($keyword) {
            $q->where('kode', 'like', "%{$keyword}%")
              ->orWhere('nama', 'like', "%{$keyword}%");
        });
    }

    /** Urutkan default berdasarkan kode */
    public function scopeDefaultOrder($query): void
    {
        $query->orderBy('kode');
    }
}
