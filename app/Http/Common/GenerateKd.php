<?php
namespace App\Http\Common;
class GenerateKd
{
    public function generateKdJurusan($nama_jurusan)
    {
        $excludeWords = ['dan', 'atau', '&', '|', 'dan.', 'atau.', '.'];

        $words = explode(' ', $nama_jurusan);

        $kd_jurusan = '';

        foreach ($words as $word) {
            $cleanedWord = preg_replace('/[^A-Za-z0-9]/', '', $word);

            if (in_array(strtolower($cleanedWord), $excludeWords)) {
                continue;
            }

            $kd_jurusan .= strtoupper(substr($cleanedWord, 0, 1));
        }

        return $kd_jurusan;
    }
}
?>
