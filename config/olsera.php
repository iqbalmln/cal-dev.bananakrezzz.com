<?php

/*
|--------------------------------------------------------------------------
| Kredensial Olsera per Cabang
|--------------------------------------------------------------------------
|
| Diisi lewat .env dengan pola bernomor, contoh untuk cabang pertama:
|
|   OLSERA_CABANG_1_NAMA="Cabang Pusat"
|   OLSERA_CABANG_1_APP_ID=xxxxxxxx
|   OLSERA_CABANG_1_SECRET_KEY=yyyyyyyy
|
| Lalu jalankan `php artisan olsera:kredensial` untuk memindahkannya ke
| tabel cabangs (secret key disimpan terenkripsi). Menambah cabang baru
| cukup menambah blok nomor berikutnya lalu jalankan command itu lagi.
|
*/

$cabang = [];

// Slot 1-10 dibaca; yang app_id-nya kosong dilewati begitu saja.
foreach (range(1, 10) as $i) {
    $appId = env("OLSERA_CABANG_{$i}_APP_ID");

    if (blank($appId)) {
        continue;
    }

    $cabang[] = [
        'slot' => $i,
        'nama' => env("OLSERA_CABANG_{$i}_NAMA"),
        'app_id' => $appId,
        'secret_key' => env("OLSERA_CABANG_{$i}_SECRET_KEY"),
    ];
}

return [
    'cabang' => $cabang,
];
