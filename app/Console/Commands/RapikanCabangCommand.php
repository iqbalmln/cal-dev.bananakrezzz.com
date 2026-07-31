<?php

namespace App\Console\Commands;

use App\Models\Cabang;
use App\Models\Invoice;
use App\Models\Rombongan;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Gabungkan cabang yang namanya kembar menjadi satu.
 *
 * Baris dengan id TERKECIL yang dipertahankan, karena baris itulah yang
 * biasanya sudah ditunjuk oleh users.cabang_id dan data lama. Kredensial
 * dan last_sync diambil dari baris kembarannya kalau yang utama masih kosong,
 * lalu seluruh rombongan/invoice/user dipindahkan sebelum kembarannya dihapus.
 */
class RapikanCabangCommand extends Command
{
    protected $signature = 'cabang:rapikan
                            {--dry-run : Tampilkan rencana tanpa mengubah apa pun}';

    protected $description = 'Gabungkan cabang duplikat (nama kembar) menjadi satu baris';

    public function handle(): int
    {
        $kembar = Cabang::orderBy('id')->get()
            ->groupBy('nama')
            ->filter(fn($rows) => $rows->count() > 1);

        if ($kembar->isEmpty()) {
            $this->info('✅ Tidak ada cabang duplikat.');
            return self::SUCCESS;
        }

        $kering = $this->option('dry-run');
        $baris = [];

        foreach ($kembar as $nama => $rows) {
            $utama = $rows->first();
            $lain = $rows->slice(1);
            $idLain = $lain->pluck('id')->all();

            $jumlahRombongan = Rombongan::whereIn('cabang_id', $idLain)->count();
            $jumlahInvoice = Invoice::whereIn('cabang_id', $idLain)->count();
            $jumlahUser = User::whereIn('cabang_id', $idLain)->count();

            // Kredensial diambil dari kembaran kalau baris utama masih kosong.
            $sumber = $lain->first(fn($c) => filled($c->olsera_app_id));
            $ambilKredensial = blank($utama->olsera_app_id) && $sumber;

            // last_sync paling baru di antara semua kembaran.
            $syncTerbaru = $rows->pluck('last_sync')->filter()->max();

            if (!$kering) {
                if ($ambilKredensial) {
                    $utama->olsera_app_id = $sumber->olsera_app_id;
                    $utama->olsera_secret_key = $sumber->olsera_secret_key;
                }

                $utama->last_sync = $syncTerbaru;
                $utama->save();

                Rombongan::whereIn('cabang_id', $idLain)->update(['cabang_id' => $utama->id]);
                Invoice::whereIn('cabang_id', $idLain)->update(['cabang_id' => $utama->id]);
                User::whereIn('cabang_id', $idLain)->update(['cabang_id' => $utama->id]);

                Cabang::whereIn('id', $idLain)->delete();
            }

            $baris[] = [
                $nama,
                $utama->id,
                implode(', ', $idLain),
                $ambilKredensial ? "diambil dari id {$sumber->id}" : ($utama->olsera_app_id ? 'sudah ada' : 'tetap kosong'),
                "{$jumlahRombongan} rombongan, {$jumlahInvoice} invoice, {$jumlahUser} user",
            ];
        }

        $this->newLine();
        $this->table(
            ['Cabang', 'Disimpan (id)', 'Dihapus (id)', 'Kredensial', 'Data dipindahkan'],
            $baris
        );

        if ($kering) {
            $this->comment('Mode dry-run: belum ada yang diubah. Jalankan tanpa --dry-run untuk menerapkan.');
            return self::SUCCESS;
        }

        $this->info('✅ Cabang duplikat digabungkan.');
        $this->line('Periksa hasilnya: php artisan olsera:kredensial --dry-run');

        return self::SUCCESS;
    }
}
