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

// Jalankan command olsera:sync setiap 5 menit
Schedule::command('olsera:sync')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

// Tes cron — akan mencatat log tiap menit
Schedule::call(function () {
    \Illuminate\Support\Facades\Log::info('🔥 Cron test executed at ' . now());
})->everyMinute();
