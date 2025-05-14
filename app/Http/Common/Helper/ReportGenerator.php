<?php

use App\Http\Common\Helper\Generate\GenerateJurusanCode;
use App\Http\Common\Helper\Generate\GenerateKaryawanCode;
use App\Http\Common\Helper\Generate\GenerateMuridCode;

class ReportGenerator
{
    public function generate(string $type, ?array $data = [])
    {
        switch ($type) {
            case ("kdJurusan"):
                $generator = new GenerateJurusanCode($data[0]);
                break;
            case ("kdStudent"):
                $generator = new GenerateMuridCode();
                break;
            case ("kdKaryawan"):
                $generator = new GenerateKaryawanCode();
                break;
            default:
                $generator = "";
                break;
        }

        return $generator;
    }
}
