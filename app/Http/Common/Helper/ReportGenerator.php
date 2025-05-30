<?php

namespace App\Http\Common\Helper;

use App\Http\Common\Helper\Generate\GenerateAgendaUpacaraCode;
use App\Http\Common\Helper\Generate\GenerateJurusanCode;
use App\Http\Common\Helper\Generate\GenerateKaryawanCode;
use App\Http\Common\Helper\Generate\GenerateMataPelajaranCode;
use App\Http\Common\Helper\Generate\GenerateMuridCode;
use App\Http\Common\Helper\Generate\GeneratePengajarCode;
use App\Http\Common\Helper\Generate\GenerateRuanganCode;

class ReportGenerator
{
    public function generator(string $type, ?array $data = []): string
    {
        switch ($type) {
            case ("kdJurusan"):
                $generator = new GenerateJurusanCode((string) $data[0]);
                break;
            case ("kdSiswa"):
                $generator = new GenerateMuridCode();
                break;
            case ("kdKaryawan"):
                $generator = new GenerateKaryawanCode();
                break;
            case ("kdRuangan"):
                $generator = new GenerateRuanganCode((string) $data[0], (string) $data[1]);
                break;
            case ("kdAgendaUpacara"):
                $generator = new GenerateAgendaUpacaraCode();
                break;
            case ("idMataPelajaran"):
                $generator = new GenerateMataPelajaranCode();
                break;
            case ("idPengajar"):
                $generator = new GeneratePengajarCode();
                break;
            default:
                $generator = "";
                break;
        }

        return $generator->generate();
    }
}
