<?php

namespace App\Console\Commands;

use App\Jobs\SyncOlseraJob;
use App\Models\Cabang;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncOlseraCommand extends Command
{
    protected $signature = 'olsera:sync
                            {--cabang= : Sinkronkan satu cabang saja (ID cabang)}
                            {--date= : Tanggal Y-m-d, default hari ini}
                            {--langsung : Jalankan sekarang juga tanpa lewat queue}';

    protected $description = 'Sinkronkan data Olsera semua cabang ke database lokal';

    public function handle(): int
    {
        $tanggal = $this->option('date') ?: now()->format('Y-m-d');

        $query = Cabang::siapSync();

        if ($id = $this->option('cabang')) {
            $query->where('id', $id);
        }

        $cabangs = $query->get();

        if ($cabangs->isEmpty()) {
            $this->warn('Tidak ada cabang dengan kredensial Olsera yang aktif.');
            Log::warning('[CRON] olsera:sync dilewati, tidak ada cabang siap sync.');
            return self::SUCCESS;
        }

        foreach ($cabangs as $cabang) {
            $job = new SyncOlseraJob($cabang, $tanggal);

            if ($this->option('langsung')) {
                dispatch_sync($job);
                $this->info("✔ {$cabang->nama} selesai disinkronkan ({$tanggal}).");
            } else {
                dispatch($job);
                $this->info("→ {$cabang->nama} dimasukkan ke antrian ({$tanggal}).");
            }
        }

        return self::SUCCESS;
    }
}
