<?php

use App\Http\Common\Helpper\Generate\GenerateKdJurusan;
use App\Http\Common\Helpper\Generate\generateStudentCode;

class Helpper
{

    public function generate(string $type, array $data)
    {
        switch ($type) {
            case ("kdJurusan"):
                $generator = new GenerateKdJurusan($data[0]);
                break;
            case ("kdStudent"):
                $generator = new generateStudentCode();
                break;
            case ("kdKaryawan"):
                $generator = "";
                break;
            default:
                $generator = "";
                break;
        }

        return $generator;
    }
}
