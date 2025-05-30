<?php

namespace App\Http\Controllers\RuangKelas;

use App\Http\Common\Helper\ReportGenerator;
use App\Http\Common\Utils\ApiResponse;
use App\Http\Common\Utils\Filtering;
use App\Http\Controllers\Controller;
use App\Models\Hari;
use App\Models\Jadwal;
use App\Models\JamBelajar;
use App\Models\RuanganKelas;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RuangKelasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $data = (new Filtering($request))
                ->setBuilder(RuanganKelas::with('jurusan', 'waliKelas'), 'nama_jurusan', 'kd_jurusan')
                ->apply();

            return (new ApiResponse(200, [$data], "Class Room fetched Successfully"))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching ruang kelas: ' . $e->getMessage());
            return (new ApiResponse(500, [], "Internal server Error"))->send();
        }
    }


    public function store(Request $request)
    {
        try {
            $generator = new ReportGenerator();
            $validator = Validator::make($request->all(), [
                'nomor_ruangan' => 'required|string|max:50',
                'kd_jurusan' => 'required|string|exists:jurusans,kd_jurusan',
                'kd_wali_kelas' => 'required'
            ]);

            if ($validator->fails()) {
                return (new ApiResponse(400, [], 'Error 400 Bad Request' . $validator->errors()))->send();
            }

            $kdJurusan = $request->kd_jurusan;
            $nomorRuangan = $request->nomor_ruangan;
            $listgenerate = [$nomorRuangan, $kdJurusan,];
            $generateNamaRuangan = $generator->generator("kdRuangan",  $listgenerate);
            $existingRuangan = RuanganKelas::where('nama_ruangan', $generateNamaRuangan)->first();

            if ($existingRuangan) {
                return (new ApiResponse(400, [],  'Nama Ruangan sudah ada'))->send();
            }

            $data = RuanganKelas::create([
                'nama_ruangan' => $generateNamaRuangan,
                'kd_jurusan' =>  $kdJurusan,
                'kd_wali_kelas' => $request->kd_wali_kelas,
                'status' => true,
            ]);

            return (new ApiResponse(201, [$data],   'Ruang kelas created successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error creating Ruang Kelas: ' . $e->getMessage());
            return (new ApiResponse(500, [],   'Internal server error' . $e->getMessage() . $request->kd_jurusan . $request->nomor_ruangan))->send();
        }
    }

    public function show(string $id)
    {
        try {

            $data = RuanganKelas::with('jurusan')->findOrFail($id);
            return (new ApiResponse(200, [$data],  'Ruangan kelas retrieved successfully'))->send();
        } catch (ModelNotFoundException $e) {
            Log::error('Ruang kelas not found: ' . $e->getMessage());
            return (new ApiResponse(400, [],  'Ruangan kelas not found'))->send();
        } catch (\Exception $e) {
            Log::error('Error retrieving jurusan: ' . $e->getMessage());
            return (new ApiResponse(500, [],   'Failed to retrieve ruang kelas'))->send();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $generator = new ReportGenerator();
            $validator = Validator::make($request->all(), [
                'kd_jurusan' => 'required',
                'nomor_ruangan' => 'required',
                'kd_wali_kelas' => 'required',
                'status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "status_code" => 400,
                    "message" => "Error 400 Bad Request",
                    "errors" => $validator->errors()
                ], 400);
            }
            $ruangKelas = RuanganKelas::findOrFail($id);

            $kdJurusan = $request->kd_jurusan;
            $nomorRuangan = $request->nomor_ruangan;
            $listgenerate = [$nomorRuangan, $kdJurusan,];
            $generateNamaRuangan = $generator->generator("kdRuangan",  $listgenerate);
            $existing = RuanganKelas::where('nama_ruangan',  $generateNamaRuangan)
                ->where('id', '!=', $id)
                ->first();
            if ($existing) {
                return (new ApiResponse(400, [],   'Nama Ruangan sudah ada'))->send();
            }

            $updated = $ruangKelas->update([
                'nama_ruangan' =>  $generateNamaRuangan,
                'kd_jurusan' => $kdJurusan,
                'kd_wali_kelas' => $request->kd_wali_kelas,
                'status' => $request->status,
            ]);

            if (!$updated) {
                return response()->json([
                    'status' => false,
                    'status_code' => 400,
                    'message' => 'Gagal memperbarui ruang kelas'
                ], 400);
            }
            return (new ApiResponse(200, [$ruangKelas],   'Ruang kelas updated successfully'))->send();
        } catch (ModelNotFoundException $e) {
            Log::error('Ruang kelas not found for update: ' . $e->getMessage());
            return (new ApiResponse(404, [],   'Ruang Kelas not found'))->send();
        } catch (\Exception $e) {
            Log::error('Error updating ruang kelas: ' . $e->getMessage());
            return (new ApiResponse(500, [],    'Failed to update ruang kelas'))->send();
        }
    }


    public function destroy(string $id) {}
}
