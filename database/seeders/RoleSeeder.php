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
                'id_role' => Str::uuid() . '-ADMIN',
                'name'    => 'ADMIN',
            ],
            [
                'id_role' => Str::uuid() . '-Guru',
                'name'    => 'GURU',
            ],
            [
                'id_role' => Str::uuid() . '-Kurikulum',
                'name'    => 'KURIKULUM',
            ],
            [
                'id_role' => Str::uuid() .  '-KEPALASEKOLAH',
                'name'    => 'KEPALASEKOLAH',
            ],
        ]);
    }
}
