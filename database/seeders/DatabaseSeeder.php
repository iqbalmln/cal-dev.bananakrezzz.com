<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\User;
use App\Models\Rombongan;
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
        User::create([
            'nama' => 'Adam FO',
            'role' => 'fo',
            'pin' => 1111
        ]);
        User::create([
            'nama' => 'Diki Kasir',
            'role' => 'kasir',
            'pin' => 2222
        ]);
        User::create([
            'nama' => 'Febi BO',
            'role' => 'bo',
            'pin' => 3333
        ]);

        // Rombongan::factory(10)->create();
       // Invoice::factory(10)->create();
    }

}
