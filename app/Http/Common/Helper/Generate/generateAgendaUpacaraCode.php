<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;
use App\Models\AgendaUpacara;

class GenerateAgendaUpacaraCode implements InterfaceGenerator
{

    public function generate(): string
    {
        $latest = AgendaUpacara::orderBy('kd_agendaupacara', 'desc')->first();

        if ($latest && preg_match('/S-(\d+)/', $latest->kd_agendaupacara, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'A-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
