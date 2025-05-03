<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Rombongan;
use App\Models\Cabang;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Cabang::create([
            'nama' => 'Jakarta',
        ]);
        Cabang::create([
            'nama' => 'Tangerang',
        ]);
        
        User::create([
            'nama' => 'Master Utama',
            'role' => 'master',
            'pin' => 1000,
        ]);
        User::create([
            'nama' => 'Adam FO',
            'role' => 'fo',
            'pin' => 11,
            'cabang_id' => 1,
        ]);
        User::create([
            'nama' => 'Diki Kasir',
            'role' => 'kasir',
            'pin' => 12,
            'cabang_id' => 1,
        ]);
        User::create([
            'nama' => 'Febi BO',
            'role' => 'bo',
            'pin' => 13,
            'cabang_id' => 1,
        ]);

        User::create([
            'nama' => 'Adam FO',
            'role' => 'fo',
            'pin' => 21,
            'cabang_id' => 2,
        ]);
        User::create([
            'nama' => 'Diki Kasir',
            'role' => 'kasir',
            'pin' => 22,
            'cabang_id' => 2,
        ]);
        User::create([
            'nama' => 'Febi BO',
            'role' => 'bo',
            'pin' => 23,
            'cabang_id' => 2,
        ]);

        // Rombongan::factory(10)->create();
       // Invoice::factory(10)->create();
    }

}
