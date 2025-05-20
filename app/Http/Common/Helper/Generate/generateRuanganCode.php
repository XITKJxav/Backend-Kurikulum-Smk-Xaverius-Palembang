<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;

class GenerateRuanganCode implements InterfaceGenerator
{
    private string $noRuang;
    private string $kdJurusan;

    function __construct($noRuang, $kdJurusan)
    {
        $this->noRuang =  $noRuang;
        $this->kdJurusan = $kdJurusan;
    }

    public function generate(): string
    {
        $parts = explode('-', $this->kdJurusan);
        return $this->noRuang . '-' . strtoupper(trim($parts[0]));
    }
}
