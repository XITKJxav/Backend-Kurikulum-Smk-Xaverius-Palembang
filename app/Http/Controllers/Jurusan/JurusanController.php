<?php

namespace App\Http\Controllers\Jurusan;

use App\Http\Common\Utils\ApiResponse;
use App\Http\Common\utils\TextFormatter;
use App\Http\Controllers\Controller;
use App\Http\Common\Utils\Filtering;
use App\Models\Jurusan;
use App\Http\Common\Helper\ReportGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JurusanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $data = (new Filtering($request))
                ->setBuilder(Jurusan::query(), 'nama_jurusan', 'kd_jurusan')
                ->apply();

            return (new ApiResponse(200, [$data], "Jurusan fetched successfully"))->send();
        } catch (\Exception $e) {
            Log::error('Error fetching jurusan: ' . $e->getMessage());
            return (new ApiResponse(500, [],  'Failed to fetch jurusan'))->send();
        }
    }

    public function store(Request $request)
    {
        try {
            $textFormat = new TextFormatter($request->nama_jurusan);
            $request->validate([
                'nama_jurusan' => 'required|string|max:255'
            ]);

            $existingJurusan = Jurusan::where('nama_jurusan', $request->nama_jurusan)->first();

            if ($existingJurusan) {
                return (new ApiResponse(400, [], 'Jurusan already registered'))->send();
            }
            $kd_jurusan  = (new ReportGenerator())->generator("kdJurusan", [$request->nama_jurusan]);

            $jurusan = Jurusan::create([
                'kd_jurusan' => $kd_jurusan,
                'nama_jurusan' =>  $textFormat->properCase($request->nama_jurusan),
                'status' => true,
            ]);

            return (new ApiResponse(201, [$jurusan], "Jurusan fetched successfully"))->send();
        } catch (\Exception $e) {
            Log::error('Error creating jurusan: ' . $e->getMessage());
            return response()->json([
                "status" => false,
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $jurusan = Jurusan::findOrFail($id);
            return (new ApiResponse(200, [$jurusan], 'Jurusan retrieved successfully'))->send();
        } catch (ModelNotFoundException $e) {
            Log::error('Jurusan not found: ' . $e->getMessage());
            return (new ApiResponse(404, [], 'Jurusan not found'))->send();
        } catch (\Exception $e) {
            Log::error('Error retrieving jurusan: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Failed to retrieve jurusan'))->send();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $jurusan = Jurusan::where('kd_jurusan', $id)->firstOrFail();

            $jurusan->update([
                'status' => $request->status,
            ]);
            return (new ApiResponse(200, [$jurusan], 'Jurusan updated successfully'))->send();
        } catch (ModelNotFoundException $e) {
            Log::error('Jurusan not found for update: ' . $e->getMessage());
            return (new ApiResponse(404, [], 'Jurusan not found'))->send();
        } catch (\Exception $e) {
            Log::error('Error updating jurusan: ' . $e->getMessage());

            return (new ApiResponse(500, [], 'Failed to update jurusan'))->send();
        }
    }
}
