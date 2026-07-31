<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rombongans', function (Blueprint $table) {
            if (!Schema::hasColumn('rombongans', 'cabang_id')) {
                $table->unsignedBigInteger('cabang_id')->nullable()->after('id');
                $table->index('cabang_id');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'cabang_id')) {
                $table->unsignedBigInteger('cabang_id')->nullable()->after('rombongan_id');
                $table->index('cabang_id');
            }
        });

        // Semua data lama berasal dari satu cabang, jadi tandai dengan cabang pertama.
        $cabangId = DB::table('cabangs')->orderBy('id')->value('id');

        if ($cabangId) {
            DB::table('rombongans')->whereNull('cabang_id')->update(['cabang_id' => $cabangId]);
            DB::table('invoices')->whereNull('cabang_id')->update(['cabang_id' => $cabangId]);
        }
    }

    public function down(): void
    {
        Schema::table('rombongans', function (Blueprint $table) {
            $table->dropIndex(['cabang_id']);
            $table->dropColumn('cabang_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['cabang_id']);
            $table->dropColumn('cabang_id');
        });
    }
};
