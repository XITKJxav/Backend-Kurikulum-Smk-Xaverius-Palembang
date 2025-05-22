<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusagendaupcaraSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('statusagendaupacara')->insert(
            [
                [
                    'id_status_upacara' => 'SU-101',
                    'nama' => 'Pending',
                ],
                [
                    'id_status_upacara' => 'SU-102',
                    'nama' => 'Completed',
                ],
                [
                    'id_status_upacara' => 'SU-103',
                    'nama' => 'Cancelled',
                ],
            ],
        );
    }
}
