<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 50)->primary();
            $table->text('value')->nullable();
            $table->string('label', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed nilai default
        DB::table('settings')->insert([
            [
                'key'         => 'tahun_default',
                'value'       => (string) now()->year,
                'label'       => 'Tahun Kegiatan',
                'description' => 'Tahun yang digunakan secara default pada seluruh modul LCC.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'nama_kegiatan',
                'value'       => 'Lomba Cerdas Cermat MPR RI',
                'label'       => 'Nama Kegiatan',
                'description' => 'Nama resmi kegiatan yang ditampilkan pada laporan dan dokumen.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
