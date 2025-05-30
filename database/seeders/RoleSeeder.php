<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'id_role' => 'd93ks8fjq2-ADMIN',
                'name'    => 'ADMIN',
            ],
            [
                'id_role' => 'a7djw82kd1-GURU',
                'name'    => 'GURU',
            ],
            [
                'id_role' =>  '89fjw82hsk-KURIKULUM',
                'name'    => 'KURIKULUM',
            ],
            [
                'id_role' =>  '92jdke7wqx-KEPALASEKOLAH',
                'name'    => 'KEPALASEKOLAH',
            ],
        ]);
    }
}
