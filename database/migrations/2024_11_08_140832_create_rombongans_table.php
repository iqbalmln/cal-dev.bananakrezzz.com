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
        Schema::create('rombongans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('waktu_datang')->nullable();
            $table->string('waktu_pulang')->nullable();
            $table->integer('kode')->nullable();
            $table->string('status')->nullable();
            $table->text('total_belanja')->nullable();
            $table->text('total_belanja2')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rombongans');
    }
};
