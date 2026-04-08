<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sekolah_lombas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('lomba_provinsi_id', 26)->comment('FK ke lomba_provinsis');
            $table->string('kode_sekolah', 3)->comment('3 digit: 2 digit kode provinsi + 1 digit urutan (1-9)');
            $table->string('nama_sekolah', 150);
            $table->string('nomor_telepon', 25)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('lomba_provinsi_id')
                  ->references('id')
                  ->on('lomba_provinsis')
                  ->onDelete('cascade');

            // Nama sekolah harus unik dalam satu lomba provinsi
            $table->unique(['lomba_provinsi_id', 'nama_sekolah']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sekolah_lombas');
    }
};
