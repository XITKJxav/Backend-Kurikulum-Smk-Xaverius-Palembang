<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\ApiResponse;
use App\Models\Jurusan;
use App\Http\Requests\JurusanRequest;

class JurusanController extends Controller
{
public function index(Request $request)
{
    $response = new ApiResponse();

    try {
        $query = $request->search; 
        $order = $request->desc == null || $request->desc != 'true' ? 'asc' : 'desc';
        $jurusanQuery = Jurusan::query();

        if ($query) {
            $jurusanQuery->where('nama_jurusan', 'like', '%' . $query . '%');  
        }

        $data = $jurusanQuery
            ->orderBy('kd_jurusan', $order)
            ->paginate(4, ['*'], 'page');  
            
        return response()->json($response->callResponse(200, $data, 'Jurusan fetched successfully'), 200);

    } catch (ModelNotFoundException $e) {
        return response()->json($response->callResponse(404, [], 'Jurusan not found'), 404);
    } catch (\Exception $e) {
        Log::error('Error fetching jurusans: ' . $e->getMessage());
        return response()->json($response->callResponse(500, [], 'Failed to fetch jurusan'), 500);
    }
}



    public function store(request $request)
    {
        $response = new ApiResponse();
        try {
            $existingJurusan = Jurusan::where('nama_jurusan', $request->nama_jurusan)->first();

            if ($existingJurusan) {
                return response()->json($response->callResponse(400, [], 'Jurusan are registered'), 400);
            }

            $kd_jurusan = $this->generateKdJurusan($request->nama_jurusan);

            $jurusan = Jurusan::create([
                'kd_jurusan' => $kd_jurusan,
                'nama_jurusan' => $request->nama_jurusan,
                'status' => true,
            ]);

            return response()->json($response->callResponse(201, $jurusan, 'Jurusan created successfully'), 201);
        } catch (ModelNotFoundException $e) {
            return response()->json($response->callResponse(404, [], 'Jurusan'), 404);
        } catch (\Exception $e) {
            Log::error('Error creating jurusan: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Failed to create jurusan'), 500);
        }
    }

    public function show(string $id)
    {
        $response = new ApiResponse();
        try {
            $file = Jurusan::findOrFail($id);

            return response()->json($response->callResponse(200, $file, 'Jurusan'), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json($response->callResponse(404, [], 'Jurusan'), 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving file: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Internal Server Error'), 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $response = new ApiResponse();

        try {
            $jurusan = Jurusan::where('kd_jurusan', $id)->firstOrFail();
            $kd_jurusan = $this->generateKdJurusan($request->nama_jurusan);

            $jurusan->update([
                'status' => $request->status,
            ]);

            return response()->json($response->callResponse(200, $jurusan, 'Jurusan updated successfully'), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json($response->callResponse(404, [], 'Jurusan not found'), 404);
        } catch (\Exception $e) {
            Log::error('Error updating jurusan: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Failed to update jurusan'), 500);
        }
    }

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

        $kd_jurusan .= '-' . date('YmdHis');

        return $kd_jurusan;
    }
}
