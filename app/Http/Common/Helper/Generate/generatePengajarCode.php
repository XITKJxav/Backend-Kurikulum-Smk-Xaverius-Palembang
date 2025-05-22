<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\User;

class GeneratePengajarCode implements InterfaceGenerator
{
    public function generate(): string
    {
        $latest = User::orderBy('id_pengajar', 'desc')->first();

        if ($latest && preg_match('/S-(\d+)/', $latest->id_pengajar, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'P-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
