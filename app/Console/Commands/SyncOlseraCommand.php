<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OlseraService;
use App\Http\Controllers\RombonganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncOlseraCommand extends Command
{
    protected $signature = 'olsera:sync';
    protected $description = 'Sinkronkan data dari Olsera ke database lokal';

    public function handle(OlseraService $olseraService)
    {
        $this->info('Memulai sinkronisasi data Olsera...');
        $date = now()->format('Y-m-d');

        try {
            // 1️⃣ Ambil data dari API Olsera
            $result = $olseraService->getSalesItemsByDate($date);

            // 2️⃣ Panggil fungsi sync_api() agar logika penyimpanan tetap sama
            $controller = new RombonganController();
            $req = new Request(['result' => $result]);

            $controller->sync_api($req);

            DB::table('rombongans')->update(['last_sync' => Carbon::now()]);

            $this->info('Sinkronisasi berhasil. Data disimpan ke database.');
            Log::info("[CRON] Sinkronisasi Olsera sukses untuk {$date}, total: " . count($result));
        } catch (\Throwable $e) {
            Log::error("[CRON] Sinkronisasi gagal: " . $e->getMessage());
            $this->error("Gagal: " . $e->getMessage());
        }
    }
}
