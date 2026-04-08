<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_drawings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('tahun', 4)->comment('Tahun lomba');
            $table->ulid('lomba_provinsi_id')->comment('FK ke lomba_provinsis');
            $table->ulid('sekolah_lomba_id')->comment('FK ke sekolah_lombas');
            $table->ulid('lomba_babak_regu_id')->comment('FK ke lomba_babak_regus');
            $table->timestamps();

            // Unique constraint: satu sekolah hanya bisa punya satu babak regu per lomba provinsi
            $table->unique(['lomba_provinsi_id', 'sekolah_lomba_id'], 'unique_sekolah_babak');
            
            // Unique constraint: satu babak regu tidak boleh duplikat dalam satu lomba provinsi
            $table->unique(['lomba_provinsi_id', 'lomba_babak_regu_id'], 'unique_babak_regu');

            $table->foreign('lomba_provinsi_id')
                  ->references('id')
                  ->on('lomba_provinsis')
                  ->onDelete('cascade');

            $table->foreign('sekolah_lomba_id')
                  ->references('id')
                  ->on('sekolah_lombas')
                  ->onDelete('cascade');

            $table->foreign('lomba_babak_regu_id')
                  ->references('id')
                  ->on('lomba_babak_regus')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_drawings');
    }
};
