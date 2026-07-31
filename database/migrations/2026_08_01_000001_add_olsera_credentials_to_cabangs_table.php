<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            if (!Schema::hasColumn('cabangs', 'olsera_app_id')) {
                $table->string('olsera_app_id')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('cabangs', 'olsera_secret_key')) {
                $table->text('olsera_secret_key')->nullable()->after('olsera_app_id');
            }
            if (!Schema::hasColumn('cabangs', 'sync_aktif')) {
                $table->boolean('sync_aktif')->default(true)->after('olsera_secret_key');
            }
            if (!Schema::hasColumn('cabangs', 'last_sync')) {
                $table->timestamp('last_sync')->nullable()->after('sync_aktif');
            }
        });

        // Pengisian kredensial dilakukan oleh `php artisan olsera:kredensial`,
        // yang membaca OLSERA_CABANG_*_APP_ID dkk dari .env.
    }

    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->dropColumn(['olsera_app_id', 'olsera_secret_key', 'sync_aktif', 'last_sync']);
        });
    }
};
