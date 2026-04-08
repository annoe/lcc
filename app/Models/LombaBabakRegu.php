<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LombaBabakRegu extends Model
{
    use HasUlids;

    protected $table = 'lomba_babak_regus';

    protected $fillable = [
        'id',
        'nomor',
        'jenis_babak_id',
        'kode',
        'uraian',
    ];

    protected $casts = [
        'nomor' => 'integer',
    ];

    /**
     * Get the jenis babak that owns this lomba babak regu.
     */
    public function jenisBabak(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JenisBabak::class, 'jenis_babak_id');
    }
}
