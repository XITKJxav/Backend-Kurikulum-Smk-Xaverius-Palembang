<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HariSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hari')->insert([
            [
                'nama' => 'Senin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Selasa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Rabu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kamis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Jumat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
