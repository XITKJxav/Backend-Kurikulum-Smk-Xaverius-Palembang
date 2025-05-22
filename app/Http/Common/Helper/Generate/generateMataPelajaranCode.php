<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\AgendaUpacara;
use App\Models\MataPelajaran;

class GenerateMataPelajaranCode implements InterfaceGenerator
{

    public function generate(): string
    {
        $latest = MataPelajaran::orderBy('id_mata_pelajaran', 'desc')->first();

        if ($latest && preg_match('/S-(\d+)/', $latest->id_mata_pelajaran, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'MP-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
