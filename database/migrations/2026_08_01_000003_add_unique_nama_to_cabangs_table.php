<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Nama cabang dipakai sebagai kunci pencocokan oleh `olsera:kredensial`,
 * jadi tidak boleh kembar. Tanpa ini, salah ketik nama di .env diam-diam
 * membuat cabang baru alih-alih mengisi cabang yang sudah ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        $kembar = DB::table('cabangs')
            ->select('nama')
            ->groupBy('nama')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('nama');

        if ($kembar->isNotEmpty()) {
            throw new RuntimeException(
                'Masih ada cabang dengan nama kembar: ' . $kembar->implode(', ') . '. '
                . 'Jalankan dulu: php artisan cabang:rapikan'
            );
        }

        Schema::table('cabangs', function (Blueprint $table) {
            $table->unique('nama');
        });
    }

    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->dropUnique(['nama']);
        });
    }
};
