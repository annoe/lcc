<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lomba_babak_regus', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedInteger('nomor')->comment('Nomor urut babak regu');
            $table->ulid('jenis_babak_id')->comment('Relasi ke jenis babak');
            $table->string('kode', 10)->unique()->comment('Kode babak regu (contoh: P1A, P1B)');
            $table->string('uraian')->comment('Uraian babak regu');
            $table->timestamps();

            $table->foreign('jenis_babak_id')
                ->references('id')
                ->on('jenis_babaks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lomba_babak_regus');
    }
};
