<?php

namespace App\Http\Controllers\Jadwal;

use App\Http\Common\Utils\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\AgendaUpacara;
use Illuminate\Http\Request;
use App\Models\JamBelajar;
use App\Models\JamIstirahat;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function printTimeWithUpacara(Request $request)
    {
        $tanggal = $tanggal = Carbon::now('Asia/Jakarta')->format('Y-m-d');;

        $jamBelajar = JamBelajar::orderBy('jam_mulai')->get(['id', 'jam_mulai', 'jam_selesai'])->toArray();
        $jamIstirahat = JamIstirahat::orderBy('id')->get(['id', 'durasi'])->toArray();
        $adaUpacara = AgendaUpacara::whereDate('tanggal_upacara', $tanggal)->exists();

        $hasil = [];
        $durasiAsli = null;

        if (count($jamBelajar) > 0) {
            $awal = Carbon::createFromTimeString($jamBelajar[0]['jam_mulai']);
            $akhir = Carbon::createFromTimeString($jamBelajar[0]['jam_selesai']);
            $durasiAsli = $awal->diffInMinutes($akhir);
        }

        $waktuMulai = $adaUpacara
            ? Carbon::createFromTimeString('07:45:00')
            : Carbon::createFromTimeString($jamBelajar[0]['jam_mulai']);

        if ($adaUpacara) {
            $hasil[] = [
                'type' => 'upacara',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '07:45:00',
                "id_jam" => 0,
                "jam_ke" => 0,
                'durasi' => '45 menit',
            ];
        }

        $istirahatIndex = 0;

        foreach ($jamBelajar as $index => $sesi) {
            // Hitung durasi yang disesuaikan
            if ($adaUpacara) {
                if ($index <= 3) {
                    $durasi = max($durasiAsli - 10, 1);
                } else if ($index <= 6) {
                    $durasi = max($durasiAsli - 5, 1);
                } else {
                    $durasi = $durasiAsli;
                }
            } else {
                $durasi = $durasiAsli;
            }

            // Tambahkan sesi pembelajaran
            $jamSelesai = $waktuMulai->copy()->addMinutes($durasi);
            $hasil[] = [
                'type' => 'pembelajaran',
                'id_jam' => $sesi['id'],
                'jam_ke' => $index + 1,
                'jam_mulai' => $waktuMulai->format('H:i:s'),
                'jam_selesai' => $jamSelesai->format('H:i:s'),
                'durasi' => $durasi . ' menit',
            ];
            $waktuMulai = $jamSelesai->copy();

            if (in_array($index + 1, [4, 7]) && isset($jamIstirahat[$istirahatIndex])) {
                $durasiIstirahat = (int) $jamIstirahat[$istirahatIndex]['durasi'];
                $istirahatSelesai = $waktuMulai->copy()->addMinutes($durasiIstirahat);

                $hasil[] = [
                    'type' => 'istirahat',
                    'id_jam' => $jamIstirahat[$istirahatIndex]['id'],
                    'jam_mulai' => $waktuMulai->format('H:i:s'),
                    'jam_selesai' => $istirahatSelesai->format('H:i:s'),
                    'durasi' => $durasiIstirahat . ' menit',
                ];

                $waktuMulai = $istirahatSelesai->copy();
                $istirahatIndex++;
            }
        }

        return (new ApiResponse(
            200,
            [
                'jadwal' => $hasil,
                'ada_upacara' => $adaUpacara,
                'durasi_asli' => $durasiAsli,
            ],
            $adaUpacara
                ? "Jadwal dipotong 10/5 menit karena ada upacara"
                : "Jadwal normal tanpa pengurangan"
        ))->send();
    }



    function scheduleStudy(array $jamBelajar, array $jamIstirahat): array
    {
        $hasil = [];
        $durasiMenit = null;

        if (count($jamBelajar) > 0) {
            $awal = Carbon::createFromTimeString($jamBelajar[0]['jam_mulai']);
            $akhir = Carbon::createFromTimeString($jamBelajar[0]['jam_selesai']);
            $durasiMenit = $awal->diffInMinutes($akhir);
        }

        $waktuMulai = null;
        $istirahatIndex = 0;

        foreach ($jamBelajar as $index => $sesi) {
            if (!$waktuMulai) {
                $waktuMulai = Carbon::createFromTimeString($sesi['jam_mulai']);
            }

            $jamSelesai = $waktuMulai->copy()->addMinutes($durasiMenit);
            $hasil[] = [
                'type' => 'pembelajaran',
                'id_jam' => $sesi['id'],
                'jam_ke' => $index + 1,
                'jam_mulai' => $waktuMulai->format('H:i:s'),
                'jam_selesai' => $jamSelesai->format('H:i:s'),
            ];
            $waktuMulai = $jamSelesai->copy();

            if (($index + 1 == 4 || $index + 1 == 7) && isset($jamIstirahat[$istirahatIndex])) {
                $durasiIstirahat = (int) $jamIstirahat[$istirahatIndex]['durasi'];
                $istirahatSelesai = $waktuMulai->copy()->addMinutes($durasiIstirahat);

                $hasil[] = [
                    'type' => 'istirahat',
                    'id_jam' => $jamIstirahat[$istirahatIndex]['id'],
                    'jam_mulai' => $waktuMulai->format('H:i:s'),
                    'jam_selesai' => $istirahatSelesai->format('H:i:s'),
                    'durasi' => $durasiIstirahat . ' menit',
                ];

                $waktuMulai = $istirahatSelesai->copy();
                $istirahatIndex++;
            }
        }

        return $hasil;
    }


    public function printTime(Request $request)
    {
        $jamBelajar = JamBelajar::orderBy('jam_mulai')->get(['id', 'jam_mulai', 'jam_selesai'])->toArray();
        $jamIstirahat = JamIstirahat::orderBy('id')->get(['id', 'durasi'])->toArray();

        $jam = $this->scheduleStudy($jamBelajar, $jamIstirahat);

        $data = [
            'jadwal' => $jam
        ];

        return (new ApiResponse(200, $data, "Success fetch schedule"))->send();
    }





    public function updateJamBelajar(Request $request) {}

    public function updateJamIstirahat(Request $request) {}
}
