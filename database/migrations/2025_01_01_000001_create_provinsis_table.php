<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provinsis', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('kode', 2)->unique()->comment('Kode provinsi 2 digit');
            $table->string('nama', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provinsis');
    }
};
