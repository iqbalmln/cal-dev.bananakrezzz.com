<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteUsersAtMidnight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete id_session and users table data at midnight';

    /**
     * Execute the console command.
     */
  
public function handle()
{
    try {
        // Debug: Cek apakah perintah masuk
        $this->info('Command is running...');

        // Hapus id_session di tabel user
        $updated = DB::table('users')->update(['session_id' => null]);
        $this->info("$updated rows updated for id_session.");

        // Hapus seluruh data di tabel user
        $deleted = DB::table('sessions')->delete();
        $this->info("$deleted rows deleted from user table.");
    } catch (\Exception $e) {
        $this->error('Error: ' . $e->getMessage());
    }
}

}

