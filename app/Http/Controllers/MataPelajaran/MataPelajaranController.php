<?php

namespace App\Http\Controllers\MataPelajaran;

use App\Http\Common\Helper\ReportGenerator;
use App\Http\Common\Utils\ApiResponse;
use App\Http\Common\Utils\Filtering;
use App\Http\Common\Utils\TextFormatter;
use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = (new Filtering($request))
                ->setBuilder(MataPelajaran::query(), 'nama', 'id_mata_pelajaran')
                ->apply();

            return (new ApiResponse(200, [$data], 'Mata Pelajaran fetched successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching Mata Pelajaran: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to fetch Mata Pelajaran' . $e->getMessage()))->send();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $generator = new ReportGenerator();
            $kdMataPelajaran = $generator->generator("idMataPelajaran");

            $mataPelajaran = MataPelajaran::create([
                'id_mata_pelajaran' => $kdMataPelajaran,
                'nama' => (new TextFormatter($validated["nama"]))->titleCase(),
                'status' => true,
            ]);

            return (new ApiResponse(201, [$mataPelajaran], 'Mata Pelajaran created successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error storing Mata Pelajaran: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to create Mata Pelajaran: ' . $e->getMessage()))->send();
        }
    }


    public function show(string $id)
    {
        try {
            $mataPelajaran = MataPelajaran::findOrFail($id);
            return (new ApiResponse(200, [$mataPelajaran], 'Mata Pelajaran fetched successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error showing Mata Pelajaran: ' . $e->getMessage());
            return (new ApiResponse(404, [], 'Mata Pelajaran not found'))->send();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'nama' => 'sometimes|required|string|max:255',
                'status' => 'sometimes|required',
            ]);

            $mataPelajaran = MataPelajaran::findOrFail($id);

            if (isset($validated['nama'])) {
                $validated['nama'] = (new TextFormatter($validated['nama']))->titleCase();
            }

            $mataPelajaran->update($validated);

            return (new ApiResponse(200, [$mataPelajaran], 'Mata Pelajaran updated successfully'))->send();
        } catch (\Exception $e) {
            Log::error('Error updating Mata Pelajaran: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to update Mata Pelajaran'))->send();
        }
    }
}
