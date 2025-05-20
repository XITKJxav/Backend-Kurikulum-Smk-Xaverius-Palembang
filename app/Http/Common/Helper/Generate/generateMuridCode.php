<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\User;

class GenerateMuridCode implements InterfaceGenerator
{
    public function generate(): string
    {
        $latest = User::orderBy('kd_siswa', 'desc')->first();

        if ($latest && preg_match('/S-(\d+)/', $latest->kd_siswa, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'S-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
