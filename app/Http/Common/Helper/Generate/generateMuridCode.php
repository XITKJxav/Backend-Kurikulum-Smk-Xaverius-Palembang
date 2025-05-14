<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\User;

class GenerateMuridCode implements InterfaceGenerator
{
    private User $user;

    public function generate(): string
    {
        $latest = $this->user::orderBy('kd_murid', 'desc')->first();
        $lastCode = $latest ? intval(substr($latest->kd_kepengurusan_kelas, -3)) : 0;
        $newCode = str_pad($lastCode + 1, 3, '0', STR_PAD_LEFT);
        return 'S-' . $newCode;
    }
}
