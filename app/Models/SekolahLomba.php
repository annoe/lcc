<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SekolahLomba extends Model
{
    use HasUlids;

    protected $table = 'sekolah_lombas';

    protected $fillable = [
        'id',
        'lomba_provinsi_id',
        'kode_sekolah',
        'nama_sekolah',
        'nomor_telepon',
        'email',
        'keterangan',
    ];

    // ── Relations ────────────────────────────────────────────
    public function lombaProvinsi(): BelongsTo
    {
        return $this->belongsTo(LombaProvinsi::class, 'lomba_provinsi_id');
    }
}
