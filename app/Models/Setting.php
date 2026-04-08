<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'key';
    public    $incrementing = false;
    protected $keyType    = 'string';

    protected $fillable = ['key', 'value', 'label', 'description'];

    // ── Helper statis ─────────────────────────────────────────
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::find($key)?->value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
    }

    public static function defaults(): array
    {
        return static::whereIn('key', ['tahun_default', 'nama_kegiatan'])
            ->pluck('value', 'key')
            ->toArray();
    }
}
