<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\ApiResponse;
use Illuminate\Validation\ValidationException;

class FileUploadController extends Controller
{
    public function index(Request $request)
    {
        $response = new ApiResponse();
        try {
            $order = $request->desc == null || $request->desc != 'true' ? 'asc' : 'desc';
            $data = FileUpload::orderBy('name', $order)->simplePaginate($perPage = 2, $columns = ['*'], $pageName = 'file');

            return response()->json($response->callResponse(200, $data, 'Jurusan fetched successfully'), 200);
        } catch (\Exception $e) {
            Log::error('Error fetching files: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'File'), 500);
        }
    }

    public function store(FileUploadRequest $request)
    {
        $response = new ApiResponse();
        try {
            if (!$request->hasFile('file')) {
                return response()->json($response->callResponse(400, [], 'File not provided'), 400);
            }

            $file = $request->file('file');
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueName = uniqid('', true) . '-' . $filename . '.' . $extension;

            Log::info('Preparing to store file with name: ' . $uniqueName);

            $path = $file->storeAs('uploads', $uniqueName, 'public');

            if (!$path) {
                Log::error('File storage failed for: ' . $uniqueName);
                return response()->json($response->callResponse(500, [], 'Failed to store the file'), 500);
            }

            Log::info('File stored successfully at: ' . $path);

            $uploadedFile = FileUpload::create([
                'url_id' => $uniqueName,
                'name' => $filename,
            ]);

            return response()->json($response->callResponse(201, $uploadedFile, 'File uploaded successfully'), 201);
        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Internal Server Error'), 500);
        }
    }

    public function show($id)
    {
        $response = new ApiResponse();
        try {
            $file = FileUpload::findOrFail($id);
            $filePath = 'uploads/' . $file->url_id;

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json($response->callResponse(404, [], 'File not found'), 404);
            }

            return response()->json($response->callResponse(200, $file, 'File'), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json($response->callResponse(404, [], 'File not found'), 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving file: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Internal Server Error'), 500);
        }
    }
    
    public function destroy(string $id)
    {
        $response = new ApiResponse();
        try {
            $file = FileUpload::where('url_id', $id)->first();

            if (!$file) {
                Log::error('File not found in the database with url_id: ' . $id);
                return response()->json($response->callResponse(404, [], 'File not found in database'), 404);
            }

            $filePath = 'uploads/' . $file->url_id;

            Log::info('Looking for file at: ' . storage_path('app/public/' . $filePath));

            if (Storage::disk('public')->exists($filePath)) {
                Log::info('File exists, proceeding to delete: ' . $filePath);
                Storage::disk('public')->delete($filePath);
            } else {
                Log::error('File not found in storage: ' . $filePath);
                return response()->json($response->callResponse(404, [], 'File not found'), 404);
            }
            $file->delete();

            return response()->json($response->callResponse(200, [], 'File deleted successfully'), 200);
        } catch (\Exception $e) {
            Log::error('Error deleting file: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Internal Server Error'), 500);
        }
    }
}
