<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_babaks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('kode', 4)->unique()->comment('Kode jenis babak (4 karakter)');
            $table->string('nama')->comment('Nama jenis babak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_babaks');
    }
};
