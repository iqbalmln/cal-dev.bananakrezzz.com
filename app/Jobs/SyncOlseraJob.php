<?php

namespace App\Jobs;

use App\Models\Cabang;
use App\Models\Invoice;
use App\Models\Rombongan;
use App\Models\User;
use App\Services\OlseraService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncOlseraJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 900;
    public array $backoff = [60, 180];

    /**
     * Cegah cabang + tanggal yang sama disinkronkan dua kali bersamaan
     * (misal cron 5 menitan bertabrakan dengan tombol sync manual).
     */
    public int $uniqueFor = 1800;

    public function __construct(
        public Cabang $cabang,
        public string $tanggal,
        public ?int $userId = null,
    ) {
    }

    public function uniqueId(): string
    {
        return "olsera-sync-{$this->cabang->id}-{$this->tanggal}";
    }

    public function handle(OlseraService $olsera): void
    {
        if (!$this->cabang->bolehSync()) {
            Log::warning("⚠️ Sync dilewati, kredensial cabang {$this->cabang->nama} belum lengkap.");
            return;
        }

        $items = $olsera->forCabang($this->cabang)->getSalesItemsByDate($this->tanggal);

        $tersimpan = $this->simpan($items);

        $this->cabang->update(['last_sync' => now()]);

        Log::info("[SYNC] {$this->cabang->nama} tanggal {$this->tanggal}: {$tersimpan} baris baru dari " . count($items) . " diterima.");
    }

    /**
     * Simpan hasil tarikan Olsera ke rombongan + invoice milik cabang ini.
     */
    protected function simpan(array $items): int
    {
        if (empty($items)) {
            return 0;
        }

        // invoices.user_id NOT NULL, jadi harus selalu dapat pemilik.
        $userId = $this->userId
            ?? $this->cabang->users()->orderBy('id')->value('id')
            ?? User::orderBy('id')->value('id');

        if (!$userId) {
            Log::error("❌ [{$this->cabang->nama}] Tidak ada user sama sekali, hasil sync tidak bisa disimpan.");
            return 0;
        }

        $tersimpan = 0;

        foreach ($items as $value) {
            $sudahAda = Invoice::where('cabang_id', $this->cabang->id)
                ->where('id_pos', $value['id_pos'])
                ->exists();

            if ($sudahAda) {
                continue;
            }

            $rombongan = Rombongan::where('cabang_id', $this->cabang->id)
                ->where('nama', $value['name'])
                ->whereDate('created_at', $value['order_date'])
                ->first();

            if ($rombongan) {
                $rombongan->increment('total_belanja', $value['price']);
            } else {
                $rombongan = Rombongan::create([
                    'cabang_id' => $this->cabang->id,
                    'nama' => $value['name'],
                    'kode' => 1,
                    'total_belanja' => $value['price'],
                    'status' => 'transaksi',
                    'waktu_datang' => $value['order_time'],
                    'created_at' => $value['order_date'],
                ]);
            }

            Invoice::create([
                'cabang_id' => $this->cabang->id,
                'user_id' => $userId,
                'rombongan_id' => $rombongan->id,
                'belanja' => $value['price'],
                'id_pos' => $value['id_pos'] ?? null,
            ]);

            $tersimpan++;
        }

        return $tersimpan;
    }

    public function failed(\Throwable $e): void
    {
        Log::error("[SYNC] Gagal sinkron cabang {$this->cabang->nama} tanggal {$this->tanggal}: " . $e->getMessage());
    }
}
