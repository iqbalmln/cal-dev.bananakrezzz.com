<?php

namespace App\Console\Commands;

use App\Models\Cabang;
use App\Services\OlseraService;
use Illuminate\Console\Command;

class OlseraKredensialCommand extends Command
{
    protected $signature = 'olsera:kredensial
                            {--dry-run : Tampilkan rencana perubahan tanpa menyimpan}';

    protected $description = 'Pindahkan kredensial Olsera dari .env ke tabel cabangs';

    public function handle(): int
    {
        $daftar = collect(config('olsera.cabang'));

        $lengkap = $daftar->filter(
            fn($item) => filled($item['nama']) && filled($item['app_id']) && filled($item['secret_key'])
        );

        $kurang = $daftar->diffKeys($lengkap);

        foreach ($kurang as $item) {
            $this->warn("⚠️ Slot {$item['slot']} dilewati: nama/app_id/secret_key belum lengkap di .env.");
        }

        if ($lengkap->isEmpty()) {
            $this->error('Tidak ada kredensial Olsera yang lengkap di .env. Lihat config/olsera.php untuk polanya.');
            return self::FAILURE;
        }

        $baris = [];

        foreach ($lengkap as $item) {
            $cabang = Cabang::firstOrNew(['nama' => $item['nama']]);

            $status = match (true) {
                !$cabang->exists => 'dibuat',
                $cabang->olsera_app_id !== $item['app_id'] => 'kredensial diganti',
                $cabang->olsera_secret_key !== $item['secret_key'] => 'secret key diganti',
                default => 'sudah sesuai',
            };

            if (!$this->option('dry-run')) {
                if (!$cabang->exists) {
                    $cabang->sync_aktif = true;
                }

                $cabang->olsera_app_id = $item['app_id'];
                $cabang->olsera_secret_key = $item['secret_key'];
                $cabang->save();

                // Kredensial berubah -> token lama tidak berlaku lagi.
                OlseraService::lupakanTokenCabang($cabang);
            }

            $baris[] = [
                $item['slot'],
                $item['nama'],
                $item['app_id'],
                str_repeat('•', 8) . substr($item['secret_key'], -4),
                $status,
            ];
        }

        $this->newLine();
        $this->table(['Slot', 'Cabang', 'App ID', 'Secret Key', 'Status'], $baris);

        if ($this->option('dry-run')) {
            $this->comment('Mode dry-run: tidak ada yang disimpan.');
            return self::SUCCESS;
        }

        $this->info('✅ Kredensial tersimpan. Cek dengan: php artisan olsera:sync --langsung');

        return self::SUCCESS;
    }
}
