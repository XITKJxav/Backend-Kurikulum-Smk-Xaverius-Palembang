<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Common\Helper\ReportGenerator;
use App\Http\Common\Utils\ApiResponse;
use App\Http\Common\Utils\Filtering;
use App\Http\Controllers\Controller;
use App\Models\Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PengajarController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = (new Filtering($request))
                ->setBuilder(Pengajar::query(), 'kd_karyawan', 'id_mata_pelajaran')
                ->apply();

            return (new ApiResponse(200, [$data], 'Pengajar fetched successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching Pengajar: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to fetch Pengajar: ' . $e->getMessage()))->send();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kd_karyawan' => 'required|string|max:50',
                'id_mata_pelajaran' => 'required|string|max:50',
                'status' => 'required|string|max:20',
            ]);

            $generator = new ReportGenerator();
            $idPengajar = $generator->generator("idPengajar");

            $pengajar = Pengajar::create([
                'id_pengajar' => $idPengajar,
                'kd_karyawan' => $validated['kd_karyawan'],
                'id_mata_pelajaran' => $validated['id_mata_pelajaran'],
                'status' => $validated['status'],
            ]);

            return (new ApiResponse(201, [$pengajar], 'Pengajar created successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error storing Pengajar: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to create Pengajar'))->send();
        }
    }

    public function show(string $id)
    {
        try {
            $pengajar = Pengajar::findOrFail($id);
            return (new ApiResponse(200, [$pengajar], 'Pengajar fetched successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error showing Pengajar: ' . $e->getMessage());
            return (new ApiResponse(404, [], 'Pengajar not found'))->send();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'kd_karyawan' => 'sometimes|required|string|max:50',
                'id_mata_pelajaran' => 'sometimes|required|string|max:50',
                'status' => 'sometimes|required|string|max:20',
            ]);

            $pengajar = Pengajar::findOrFail($id);
            $pengajar->update($validated);

            return (new ApiResponse(200, [$pengajar], 'Pengajar updated successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error updating Pengajar: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to update Pengajar'))->send();
        }
    }
}
