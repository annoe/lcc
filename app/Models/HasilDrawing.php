<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilDrawing extends Model
{
    use HasUlids;

    protected $table = 'hasil_drawings';

    protected $fillable = [
        'id',
        'tahun',
        'lomba_provinsi_id',
        'sekolah_lomba_id',
        'lomba_babak_regu_id',
    ];

    /**
     * Get the lomba provinsi for this drawing result.
     */
    public function lombaProvinsi(): BelongsTo
    {
        return $this->belongsTo(LombaProvinsi::class, 'lomba_provinsi_id');
    }

    /**
     * Get the sekolah lomba for this drawing result.
     */
    public function sekolahLomba(): BelongsTo
    {
        return $this->belongsTo(SekolahLomba::class, 'sekolah_lomba_id');
    }

    /**
     * Get the lomba babak regu for this drawing result.
     */
    public function lombaBabakRegu(): BelongsTo
    {
        return $this->belongsTo(LombaBabakRegu::class, 'lomba_babak_regu_id');
    }
}
