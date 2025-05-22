<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class jamBelajarSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jam_belajar')->insert([
            [
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '07:45:00'
            ],
            [
                'jam_mulai' => '07:45:00',
                'jam_selesai' => '08:30:00'
            ],
            [
                'jam_mulai' => '08:30:00',
                'jam_selesai' => '09:15:00'
            ],

            [
                'jam_mulai' => '09:15:00',
                'jam_selesai' => '10:00:00'
            ],
            [
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '10:45:00'
            ],
            [
                'jam_mulai' => '10:45:00',
                'jam_selesai' => '11:30:00'
            ],
            [
                'jam_mulai' => '11:30:00',
                'jam_selesai' => '12:15:00'
            ],
            [
                'jam_mulai' => '12:15:00',
                'jam_selesai' => '13:00:00'
            ],
            [
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '13:45:00'
            ],
            [
                'jam_mulai' => '13:45:00',
                'jam_selesai' => '14:30:00'
            ]
        ]);
    }
}
