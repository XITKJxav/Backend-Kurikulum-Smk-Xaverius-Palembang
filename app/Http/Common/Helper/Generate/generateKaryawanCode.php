<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\Karyawan;

class GenerateKaryawanCode implements InterfaceGenerator
{
    private Karyawan $karyawan;

    public function generate(): string
    {
        $latest = Karyawan::orderBy('kd_karyawan', 'desc')->first();

        if ($latest) {
            $lastNumberStr = substr($latest->kd_karyawan, 2);
            $lastNumber = intval($lastNumberStr);
        } else {
            $lastNumber = 0;
        }

        $newNumber = $lastNumber + 1;
        $newCodeNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        return 'K-' . $newCodeNumber;
    }
}
