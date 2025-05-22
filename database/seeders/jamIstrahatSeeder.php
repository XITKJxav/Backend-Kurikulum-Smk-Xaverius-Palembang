<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class jamIstrahatSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('jam_istirahat')->insert(
            [
                [
                    'durasi' => 15,
                ],
                [
                    'durasi' => 25
                ],
            ]

        );
    }
}
