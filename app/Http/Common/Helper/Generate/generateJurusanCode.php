<?php

namespace App\Http\Common\Helper\Generate;

use App\Http\Common\Helper\interfaces\InterfaceGenerator;


class GenerateJurusanCode implements InterfaceGenerator
{
    private string $nama_jurusan;

    function __construct($nama_jurusan)
    {
        $this->nama_jurusan = $nama_jurusan;
    }

    public function generate(): string
    {
        $excludeWords = ['dan', 'atau', '&', '|', 'dan.', 'atau.', '.'];
        $words = explode(' ', $this->nama_jurusan);
        $kd_jurusan = '';

        foreach ($words as $word) {
            $cleanedWord = preg_replace('/[^A-Za-z0-9]/', '', $word);
            if (in_array(strtolower($cleanedWord), $excludeWords)) {
                continue;
            }

            $kd_jurusan .= strtoupper(substr($cleanedWord, 0, 1));
        }

        $kd_jurusan .= '-' . date('YmdHis');

        return $kd_jurusan;
    }
}
