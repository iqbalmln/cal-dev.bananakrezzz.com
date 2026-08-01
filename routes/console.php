<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Default Laravel command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// ===============================
// 🔔 Custom Scheduled Tasks
// ===============================

// Jalankan command delete:users setiap jam 00:00
Schedule::command('delete:users')->dailyAt('00:00');

// Jalankan command olsera:sync setiap 5 menit.
// Command ini hanya memasukkan job ke antrian, jadi selesainya cepat.
// Angka pada withoutOverlapping() = umur kunci dalam menit. Wajib diisi:
// tanpa itu kuncinya bertahan 24 jam, sehingga kalau prosesnya mati mendadak
// (server reboot / ter-kill) jadwalnya tidak akan menyala lagi seharian.
Schedule::command('olsera:sync')
    ->everyFiveMinutes()
    ->withoutOverlapping(10)
    ->onOneServer()
    ->runInBackground();

// Queue worker "nebeng" cron: tiap menit worker dinyalakan, menghabiskan antrian,
// lalu mati sendiri. Tidak perlu supervisor/systemd di server.
Schedule::command('queue:work --stop-when-empty --max-time=280 --tries=3')
    ->everyMinute()
    ->withoutOverlapping(10)
    ->onOneServer()
    ->runInBackground();
