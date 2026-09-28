<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Default Laravel command
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===============================
// 🔔 Custom Scheduled Tasks
// ===============================
//
// Catatan hosting: server ini memblokir proc_open (cek `php -i | grep
// disable_functions`). Schedule::command() menjalankan tiap task sebagai
// sub-proses lewat Symfony Process, jadi di sini selalu gagal dengan
// "The Process class relies on proc_open". Karena itu semua task dipanggil
// dengan Schedule::call() + Artisan::call(), yang jalan di dalam proses cron
// itu sendiri sehingga tidak butuh proc_open sama sekali.

// Jalankan command delete:users setiap jam 00:00
Schedule::call(fn () => Artisan::call('delete:users'))->dailyAt('00:00');

// Sinkronisasi Olsera setiap 5 menit.
// Opsi --langsung memakai dispatch_sync(), jadi job dikerjakan saat itu juga
// tanpa menunggu worker. Ini perlu karena queue:work pun butuh proc_open dan
// tidak bisa dijadwalkan di hosting ini.
// Angka pada withoutOverlapping() = umur kunci dalam menit. Wajib diisi:
// tanpa itu kuncinya bertahan 24 jam, sehingga kalau prosesnya mati mendadak
// (server reboot / ter-kill) jadwalnya tidak akan menyala lagi seharian.
// name() wajib sebelum withoutOverlapping() pada Schedule::call(): closure
// tidak punya nama alami seperti command, jadi kunci mutex-nya harus diberi
// nama sendiri.
Schedule::call(fn () => Artisan::call('olsera:sync', ['--langsung' => true]))
    ->name('olsera-sync')
    ->everyFiveMinutes()
    ->withoutOverlapping(10);

// Jadwal queue:work dihapus: sama-sama gagal karena proc_open, dan sudah tidak
// diperlukan untuk Olsera sejak sync berjalan langsung di atas. Kalau nanti ada
// job lain yang tetap lewat antrian, jalankan worker sebagai cron entry
// tersendiri di hPanel (cron memanggil PHP langsung, tanpa proc_open):
//
//   /opt/alt/php84/usr/bin/php /home/u599127144/domains/bananakrezzz.com/\
//   public_html/cal-dev.bananakrezzz.com/artisan queue:work \
//   --stop-when-empty --max-time=55 --tries=3