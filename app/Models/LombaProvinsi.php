<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LombaProvinsi extends Model
{
    use HasUlids;

    protected $table = 'lomba_provinsis';

    protected $fillable = [
        'id',
        'tahun',
        'provinsi_id',
    ];

    // ── Relations ────────────────────────────────────────────
    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id');
    }

    public function sekolah(): HasMany
    {
        return $this->hasMany(SekolahLomba::class, 'lomba_provinsi_id')
                    ->orderBy('kode_sekolah');
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopeDefaultOrder($query): void
    {
        $query->orderBy('tahun', 'desc')
              ->orderBy('created_at', 'desc');
    }

    public function scopeByTahun($query, string $tahun): void
    {
        $query->where('tahun', $tahun);
    }
}
