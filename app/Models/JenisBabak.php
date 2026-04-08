<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class JenisBabak extends Model
{
    use HasUlids;

    protected $table = 'jenis_babaks';

    protected $fillable = [
        'id',
        'kode',
        'nama',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Seed default data when table is empty
        static::creating(function ($model) {
            if (static::count() === 0) {
                // This will be handled by seeder instead
            }
        });
    }
}
