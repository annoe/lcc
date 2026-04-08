<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lomba_provinsis', function (Blueprint $table) {
            $table->string('nama_kegiatan', 250)->nullable()->after('provinsi_id')
                  ->comment('Nama resmi kegiatan lomba (hasil substitusi template)');
            $table->string('tempat_kegiatan', 250)->nullable()->after('nama_kegiatan')
                  ->comment('Tempat/lokasi pelaksanaan kegiatan');
            $table->date('tanggal_kegiatan')->nullable()->after('tempat_kegiatan')
                  ->comment('Tanggal pelaksanaan kegiatan');
        });
    }

    public function down(): void
    {
        Schema::table('lomba_provinsis', function (Blueprint $table) {
            $table->dropColumn(['nama_kegiatan', 'tempat_kegiatan', 'tanggal_kegiatan']);
        });
    }
};
