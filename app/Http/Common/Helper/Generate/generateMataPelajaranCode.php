<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\MataPelajaran;

class GenerateMataPelajaranCode implements InterfaceGenerator
{
    public function generate(): string
    {
        $latest = MataPelajaran::orderBy('id_mata_pelajaran', 'desc')->first();

        if ($latest) {
            $lastNumberStr = substr($latest->id_mata_pelajaran, 3);
            $lastNumber = intval($lastNumberStr);
        } else {
            $lastNumber = 0;
        }

        $newNumber = $lastNumber + 1;
        $newCodeNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        return 'MP-' . $newCodeNumber;
    }
}
