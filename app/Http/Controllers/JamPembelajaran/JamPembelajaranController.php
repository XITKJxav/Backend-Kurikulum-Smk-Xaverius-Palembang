<?php

namespace App\Http\Controllers\JamPembelajaran;

use App\Http\Common\Utils\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgendaUpacara;
use App\Models\JamBelajar;
use App\Models\StatusAgendaUpacara;
use Carbon\Carbon;

class JamPembelajaranController extends Controller
{
    public function printTimeWithUpacara(Request $request)
    {
        try {
            $tanggal = Carbon::now('Asia/Jakarta')->format('Y-m-d');

            $statusCompleted = StatusAgendaUpacara::where('nama', 'Completed')->value('id_status_upacara');
            $statusCancelled = StatusAgendaUpacara::where('nama', 'Cancelled')->value('id_status_upacara');

            AgendaUpacara::whereDate('tanggal_upacara', '<', $tanggal)
                ->where('id_status_upacara', '!=', $statusCompleted)
                ->where('id_status_upacara', '!=', $statusCancelled)
                ->update(['id_status_upacara' => $statusCompleted]);

            $agendaUpacara = AgendaUpacara::with('statusAgendaUpacara')
                ->whereDate('tanggal_upacara', '>=', $tanggal)
                ->first();

            $adaUpacara = false;
            $tanggalUpacara = null;
            $idHari = null;

            if ($agendaUpacara && isset($agendaUpacara->statusAgendaUpacara)) {
                $status = strtolower($agendaUpacara->statusAgendaUpacara->nama);
                if ($status === 'pending') {
                    $adaUpacara = true;
                    $tanggalUpacara = $agendaUpacara->tanggal_upacara;
                    $idHari = $agendaUpacara->id_hari;
                }
            }

            $jamBelajar = JamBelajar::orderBy('jam_mulai')->get(['id', 'jam_mulai', 'jam_selesai'])->toArray();
            $jamIstirahat = [
                ['id' => 1, 'durasi' => 15],
                ['id' => 2, 'durasi' => 25],
            ];

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
                    'id_jam' => 0,
                    'jam_ke' => 0,
                    'durasi' => '45 menit',
                ];
            }

            $istirahatIndex = 0;

            foreach ($jamBelajar as $index => $sesi) {
                if ($adaUpacara) {
                    if ($index <= 3) {
                        $durasi = max($durasiAsli - 10, 1);
                    } elseif ($index <= 6) {
                        $durasi = max($durasiAsli - 5, 1);
                    } else {
                        $durasi = $durasiAsli;
                    }
                } else {
                    $durasi = $durasiAsli;
                }

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
                    $durasiIstirahat = (int)$jamIstirahat[$istirahatIndex]['durasi'];
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
                    'tanggal_upacara' => $tanggalUpacara,
                    'id_hari' => $idHari,
                ],
                $adaUpacara
                    ? "Jadwal dipotong 10/5 menit karena ada upacara"
                    : "Jadwal normal tanpa pengurangan"
            ))->send();
        } catch (\Exception $e) {
            return (new ApiResponse(500, [], "Error fetch schedule: " . $e->getMessage()))->send();
        }
    }


    function scheduleStudy(array $jamBelajar, array $jamIstirahat, int $idHari): array
    {
        $hasil = [];
        $durasiAsli = null;

        if (count($jamBelajar) > 0) {
            $awal = Carbon::createFromTimeString($jamBelajar[0]['jam_mulai']);
            $akhir = Carbon::createFromTimeString($jamBelajar[0]['jam_selesai']);
            $durasiAsli = $awal->diffInMinutes($akhir);
        }

        $KegiatanPagi = [
            [
                "id_hari" => 3,
                "keterangan" => "SENAM",
                "jam_mulai" => "06:40:00",
                "durasi" => 65
            ],
            [
                "id_hari" => 5,
                "keterangan" => "LITERASI",
                "jam_mulai" => "07:00:00",
                "durasi" => 45
            ],
            [
                "id_hari" => 1,
                "keterangan" => "PEMBINAAN",
                "jam_mulai" => "07:00:00",
                "durasi" => 45
            ]
        ];

        $kegiatanHariIni = collect($KegiatanPagi)->firstWhere('id_hari', $idHari);
        $waktuMulai = Carbon::createFromTimeString($jamBelajar[0]['jam_mulai']);

        if ($kegiatanHariIni) {
            $durasiKegiatan = (int)$kegiatanHariIni['durasi'];
            $mulaiKegiatan = Carbon::createFromTimeString($kegiatanHariIni['jam_mulai']);
            $selesaiKegiatan = $mulaiKegiatan->copy()->addMinutes($durasiKegiatan);

            $hasil[] = [
                'type' => $kegiatanHariIni['keterangan'],
                'jam_mulai' => $mulaiKegiatan->format('H:i:s'),
                'jam_selesai' => $selesaiKegiatan->format('H:i:s'),
                'durasi' => $durasiKegiatan . ' menit',
            ];

            if ($selesaiKegiatan->greaterThan($waktuMulai)) {
                $waktuMulai = $selesaiKegiatan->copy();
            }
        }

        $istirahatIndex = 0;
        foreach ($jamBelajar as $index => $sesi) {
            $durasi = $durasiAsli;

            if ($kegiatanHariIni && $kegiatanHariIni['keterangan'] === "SENAM") {
                if ($index <= 5) {
                    $durasi = max($durasiAsli - 5, 1);
                } elseif ($index <= 8) {
                    $durasi = max($durasiAsli - 10, 1);
                } elseif ($index <= 9) {
                    $durasi = max($durasiAsli - 5, 1);
                }
            } elseif ($kegiatanHariIni && $kegiatanHariIni['keterangan'] === "LITERASI" || $kegiatanHariIni && $kegiatanHariIni['keterangan'] === "PEMBINAN") {
                if ($index <= 3) {
                    $durasi = max($durasiAsli - 10, 1);
                } elseif ($index <= 6) {
                    $durasi = max($durasiAsli - 15, 1);
                } elseif ($index <= 8) {
                    $durasi = max($durasiAsli - 5, 1);
                }
            }

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
            $isSenam = $kegiatanHariIni && $kegiatanHariIni['keterangan'] === 'SENAM';

            if (
                (
                    ($isSenam && in_array($index, [2, 6])) ||
                    (!$isSenam && in_array($index, [3, 6]))
                )
                && isset($jamIstirahat[$istirahatIndex])
            ) {
                $durasiIstirahat = (int)$jamIstirahat[$istirahatIndex]['durasi'];
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
        try {
            $jamBelajar = JamBelajar::orderBy('jam_mulai')->get(['id', 'jam_mulai', 'jam_selesai'])->toArray();
            $jamIstirahat = [
                ['id' => 1, 'durasi' => 15],
                ['id' => 2, 'durasi' => 25],
            ];

            $idHari = $request->id_hari ?? 1;
            $data = $this->scheduleStudy($jamBelajar, $jamIstirahat, $idHari);

            return (new ApiResponse(200, $data, "Success fetch schedule"))->send();
        } catch (\Exception $e) {
            return (new ApiResponse(500, [], "Error fetch schedule: " . $e->getMessage()))->send();
        }
    }



    public function getJadwal() {}
}
