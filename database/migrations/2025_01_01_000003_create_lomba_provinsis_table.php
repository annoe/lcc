<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lomba_provinsis', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('tahun', 4)->comment('Tahun pelaksanaan LCC');
            $table->string('provinsi_id', 26)->comment('FK ke tabel provinsis (ULID)');
            $table->timestamps();

            $table->foreign('provinsi_id')
                  ->references('id')
                  ->on('provinsis')
                  ->onDelete('cascade');

            // Satu provinsi hanya boleh terdaftar sekali per tahun
            $table->unique(['tahun', 'provinsi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lomba_provinsis');
    }
};
