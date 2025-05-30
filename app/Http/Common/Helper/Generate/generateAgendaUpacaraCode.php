<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\AgendaUpacara;

class GenerateAgendaUpacaraCode implements InterfaceGenerator
{

    public function generate(): string
    {
        $latest = AgendaUpacara::orderBy('kd_agendaupacara', 'desc')->first();

        if ($latest) {
            $lastNumberStr = substr($latest->kd_agendaupacara, 2);
            $lastNumber = intval($lastNumberStr);
        } else {
            $lastNumber = 0;
        }

        $newNumber = $lastNumber + 1;
        $newCodeNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        return 'A-' . $newCodeNumber;
    }
}
