<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('jadwal')->truncate();

        $jamBelajar = DB::table('jam_belajar')->select('id')->get();
        $hari = DB::table('hari')->select('id')->get();

        $jadwalData = [];

        foreach ($jamBelajar as $jam) {
            foreach ($hari as $h) {
                $jadwalData[] = [
                    'kd_jam_pembelajaran' => $jam->id,
                    'id_hari' => $h->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('jadwal')->insert($jadwalData);
    }
}
