<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\Karyawan;

class GenerateKaryawanCode implements InterfaceGenerator
{
    private Karyawan $karyawan;

    public function generate(): string
    {
        $latest = $this->karyawan::orderBy('kd_karyawan', 'desc')->first();
        $lastCode = $latest ? intval(substr($latest->kd_kepengurusan_kelas, -3)) : 0;
        $newCode = str_pad($lastCode + 1, 3, '0', STR_PAD_LEFT);
        return 'K-' . $newCode;
    }
}
