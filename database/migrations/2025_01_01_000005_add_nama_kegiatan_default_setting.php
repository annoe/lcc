<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insert hanya jika belum ada
        if (!DB::table('settings')->where('key', 'nama_kegiatan_default')->exists()) {
            DB::table('settings')->insert([
                'key'         => 'nama_kegiatan_default',
                'value'       => 'Lomba Cerdas Cermat MPR RI Tahun {{tahun}} Seleksi Provinsi, Provinsi {{provinsi}}',
                'label'       => 'Nama Kegiatan Default',
                'description' => 'Template nama kegiatan dengan parameter {{tahun}} dan {{provinsi}}.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'nama_kegiatan_default')->delete();
    }
};
