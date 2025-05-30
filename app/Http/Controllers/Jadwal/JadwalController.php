<?php

namespace App\Http\Controllers\Jadwal;

use App\Http\Common\Utils\ApiResponse;
use App\Http\Common\Utils\Filtering;
use App\Http\Controllers\Controller;
use App\Models\Hari;
use App\Models\Jadwal;
use App\Models\JamBelajar;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JadwalController extends Controller
{

    public function getJadwal(Request $request)
    {
        try {
            $id_kelas = $request->id_ruangan_kelas;
            $id_hari = $request->id_hari;
            $id_jam = $request->kd_jam_pembelajaran;

            $data = Jadwal::with(['mataPelajaran', 'pengajar', 'jamBelajar', 'hari', 'guruPiket']);

            if ($id_kelas && $id_hari && $id_jam) {
                $data = $data->where('id_ruangan_kelas', $id_kelas)
                    ->where('id_hari', $id_hari)
                    ->where('kd_jam_pembelajaran', $id_jam);
            } elseif ($id_kelas) {
                $data = $data->where('id_ruangan_kelas', $id_kelas);
            }

            $data = $data->get();

            return (new ApiResponse(200, [$data], "Jadwal pembelajaran fetched successfully"))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching Jadwal pembelajaran: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to fetch jadwal pembelajaran'))->send();
        }
    }



    public function getDay(Request $request)
    {
        try {
            $data = Hari::all();

            return (new ApiResponse(200, [$data], "Day fetched successfully"))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching agenda upacara: ' . $e->getMessage());
            return (new ApiResponse(500, [],  'Failed to fetch Day'))->send();
        }
    }



    public function updateJadwal(Request $request)
    {
        Log::info('UpdateJadwal Request:', $request->all());

        // Validasi manual
        $validator = Validator::make($request->all(), [
            'id_ruangan_kelas'       => 'required|integer|exists:ruangankelas,id',
            'id_hari'                => 'required|integer|exists:hari,id',
            'kd_jam_pembelajaran'    => 'required|integer|exists:jam_belajar,id',
            'id_mata_pelajaran'      => 'nullable|string|exists:mata_pelajaran,id_mata_pelajaran',
            'id_pengajar'            => 'nullable|string|exists:karyawan,kd_karyawan',
            'kd_guru_piket'          => 'nullable|string|exists:karyawan,kd_karyawan',
        ]);

        // Kirim error validasi
        if ($validator->fails()) {
            return (new ApiResponse(422, [], $validator->errors()->first() . $request->id_ruangan_kelas . "jooo" . $request->id_hari))->send();
        }

        $validated = $validator->validated();

        // Ambil data
        $id_ruangan_kelas    = (int) $validated['id_ruangan_kelas'];
        $id_hari             = (int) $validated['id_hari'];
        $kd_jam_pembelajaran = (int) $validated['kd_jam_pembelajaran'];
        $id_mata_pelajaran   = $validated['id_mata_pelajaran'] ?? null;
        $id_pengajar         = $validated['id_pengajar'] ?? null;
        $kd_guru_piket       = $validated['kd_guru_piket'] ?? null;

        try {
            // Cek jadwal eksisting
            $jadwal = Jadwal::where('id_ruangan_kelas', $id_ruangan_kelas)
                ->where('id_hari', $id_hari)
                ->where('kd_jam_pembelajaran', $kd_jam_pembelajaran)
                ->first();

            if (!$jadwal) {
                return (new ApiResponse(404, [], 'Jadwal tidak ditemukan'))->send();
            }

            // Cek bentrok pengajar, kalau pengajar tidak null
            if ($id_pengajar) {
                $bentrok = Jadwal::where('id_pengajar', $id_pengajar)
                    ->where('id_hari', $id_hari)
                    ->where('kd_jam_pembelajaran', $kd_jam_pembelajaran)
                    ->where('id_ruangan_kelas', '!=', $id_ruangan_kelas)
                    ->exists();

                if ($bentrok) {
                    return (new ApiResponse(409, [], "Guru {$id_pengajar} sudah memiliki jadwal pada waktu tersebut di kelas lain"))->send();
                }
            }

            // Update data (gunakan array_filter agar null tidak diinput)
            $jadwal->update(array_filter([
                'id_mata_pelajaran' => $id_mata_pelajaran,
                'id_pengajar'       => $id_pengajar,
                'kd_guru_piket'     => $kd_guru_piket,
            ], fn($v) => !is_null($v)));

            return (new ApiResponse(200, [], 'Jadwal berhasil diperbarui'))->send();
        } catch (\Exception $e) {
            Log::error('Update schedule error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Gagal memperbarui jadwal: ' . $request->id_ruangan_kelas . $e->getMessage()))->send();
        }
    }



    public function createJadwal(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_ruangan_kelas' => ['required'],
            ]);


            $idKelas = $validated['id_ruangan_kelas'];
            $results = [];

            $hariList = Hari::all();
            $jamList = JamBelajar::all();

            foreach ($hariList as $hari) {
                foreach ($jamList as $jam) {
                    $exists = Jadwal::where([
                        'id_ruangan_kelas' => $idKelas,
                        'id_hari' => $hari->id,
                        'kd_jam_pembelajaran' => $jam->id,
                    ])->exists();

                    if (!$exists) {
                        $jadwal = Jadwal::create([
                            'id_ruangan_kelas' => $idKelas,
                            'id_hari' => $hari->id,
                            'kd_jam_pembelajaran' => $jam->id,
                            'id_mata_pelajaran' => null,
                            'id_pengajar' => null,
                            'kd_guru_piket' => null,
                        ]);
                        $results[] = $jadwal;
                    }
                }
            }

            return (new ApiResponse(201, $results, "Jadwal berhasil dibuat otomatis berdasarkan semua kombinasi hari dan jam"))->send();
        } catch (ValidationException $e) {
            return (new ApiResponse(422, [], $e->getMessage()))->send();
        } catch (\Exception $e) {
            Log::error('Create schedule error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Gagal membuat jadwal'))->send();
        }
    }
    public function fetchDurationTimeStudy(Request $request)
    {
        try {
            $jamBelajar = JamBelajar::get();
            $awal = Carbon::createFromTimeString($jamBelajar[0]['jam_mulai']);
            $akhir = Carbon::createFromTimeString($jamBelajar[0]['jam_selesai']);
            $durasiAsli = $awal->diffInMinutes($akhir);
            $data = [
                "duration_time_study" => $durasiAsli,
            ];
            return (new ApiResponse(200, $data, 'Durasi pembelajaran berhasil ditemukan'))->send();
        } catch (\Exception $e) {
            Log::error('Get duration error: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Gagal mendapatkan durasi pembelajaran'))->send();
        }
    }
    public function updateDurationTimeStudy(Request $request)
    {
        try {
            $validatedDuration = $request->duration_time_study;

            if (!is_numeric($validatedDuration) || $validatedDuration <= 0) {
                return (new ApiResponse(422, [], 'Durasi tidak valid'))->send();
            }

            $startTime = Carbon::createFromTimeString('07:00:00');
            $jamBelajar = JamBelajar::orderBy('kd_jam_pembelajaran')->get();

            foreach ($jamBelajar as $jam) {
                $jam->jam_mulai = $startTime->format('H:i');
                $jam->jam_selesai = $startTime->copy()->addMinutes($validatedDuration)->format('H:i');
                $jam->save();

                $startTime->addMinutes($validatedDuration);
            }

            return (new ApiResponse(200, $jamBelajar, "Berhasil memperbarui durasi jam belajar"))->send();
        } catch (\Exception $e) {
            return (new ApiResponse(500, [], 'Gagal memperbarui jam belajar: ' . $e->getMessage()))->send();
        }
    }
}
