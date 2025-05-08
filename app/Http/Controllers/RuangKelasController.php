<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RuanganKelas;
use Illuminate\Support\Facades\Log;
use App\Http\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RuangKelasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $response = new ApiResponse();

        try {
            $query = trim($request->search, "\"'");
            $order = $request->boolean('orderBy') ? 'desc' : 'asc';
            $offLimit = $request->offLimit;

            $ruangKelasQuery = RuanganKelas::with('jurusan');

            if (!empty($query)) {
                $ruangKelasQuery->where('nama_ruangan', 'like', '%' . $query . '%');
            }

            $data = $offLimit === 'true'
                ? $ruangKelasQuery->orderBy('nama_ruangan', $order)->get()
                : $ruangKelasQuery->orderBy('nama_ruangan', $order)->paginate(5);

            return response()->json(
                $response->callResponse(200, $data, 'Ruang kelas fetched successfully'),
                200
            );
        } catch (\Exception $e) {
            Log::error('Error fetching ruang kelas: ' . $e->getMessage());
            return response()->json(
                $response->callResponse(500, [], 'Failed to fetch ruang kelas'),
                500
            );
        }
    }



    public function store(Request $request)
    {
        $response = new ApiResponse();

        try {
            $validator = Validator::make($request->all(), [
                'nomor_ruangan' => 'required|string|max:50',
                'kd_jurusan' => 'required|string|exists:jurusans,kd_jurusan',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'status_code' => 400,
                    'message' => 'Error 400 Bad Request',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $nama_ruangan = $this->generateKdRuang($request->nomor_ruangan, $request->kd_jurusan);
            $existingRuangan = RuanganKelas::where('nama_ruangan', $nama_ruangan)->first();

            if ($existingRuangan) {
                return response()->json($response->callResponse(400, [], 'Nama Ruangan sudah ada'), 400);
            }

            $ruangKelas = RuanganKelas::create([
                'nama_ruangan' => $nama_ruangan,
                'kd_jurusan' => $request->kd_jurusan,
                'status' => true,
            ]);

            return response()->json($response->callResponse(201, $ruangKelas, 'Ruang kelas created successfully'), 201);
        } catch (\Exception $e) {
            Log::error('Error creating Ruang Kelas: ' . $e->getMessage());
            return response()->json($response->callResponse(500, []), 500);
        }
    }


    public function generateKdRuang($noRuang, $kdJurusan)
    {
        $parts = explode('-', $kdJurusan);
        return $noRuang . '-' . strtoupper(trim($parts[0]));
    }

    public function show(string $id)
    {
        $response = new ApiResponse();

        try {
            $ruangKelas = RuanganKelas::with('jurusan')->findOrFail($id);

            return response()->json($response->callResponse(200, [$ruangKelas], 'Ruangan kelas retrieved successfully'), 200);
        } catch (ModelNotFoundException $e) {
            Log::error('Ruang kelas not found: ' . $e->getMessage());
            return response()->json($response->callResponse(404, [], 'Ruangan kelas not found'), 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving jurusan: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Failed to retrieve ruang kelas'), 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $response = new ApiResponse();

        try {
            $validator = Validator::make($request->all(), [
                'kd_jurusan' => 'required',
                'nomor_ruangan' => 'required',
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
            $namaRuangan = $this->generateKdRuang($nomorRuangan, $kdJurusan);

            $existing = RuanganKelas::where('nama_ruangan', $namaRuangan)
                ->where('id', '!=', $id)
                ->first();
            if ($existing) {
                return response()->json($response->callResponse(400, [], 'Nama Ruangan sudah ada'), 400);
            }

            $updated = $ruangKelas->update([
                'nama_ruangan' => $namaRuangan,
                'kd_jurusan' => $kdJurusan,
                'status' => $request->status,
            ]);

            if (!$updated) {
                return response()->json([
                    'status' => false,
                    'status_code' => 400,
                    'message' => 'Gagal memperbarui ruang kelas'
                ], 400);
            }

            return response()->json(
                $response->callResponse(200, $ruangKelas, 'Ruang kelas updated successfully'),
                200
            );
        } catch (ModelNotFoundException $e) {
            Log::error('Ruang kelas not found for update: ' . $e->getMessage());
            return response()->json($response->callResponse(404, [], 'Ruang Kelas not found'), 404);
        } catch (\Exception $e) {
            Log::error('Error updating ruang kelas: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Failed to update ruang kelas'), 500);
        }
    }


    public function destroy(string $id)
    {
        //
    }
}
