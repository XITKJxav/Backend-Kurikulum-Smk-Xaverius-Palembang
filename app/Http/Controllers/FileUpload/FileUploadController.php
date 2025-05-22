<?php

namespace App\Http\Controllers\FileUpload;

use App\Http\Common\Utils\ApiResponse;
use App\Http\Controllers\Controller;

use App\Http\Requests\FileUploadRequest;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class FileUploadController extends Controller
{
    public function index(Request $request)
    {
        try {
            $order = ($request->query('desc') === 'true') ? 'desc' : 'asc';
            $data = FileUpload::orderBy('name', $order)->simplePaginate(2, ['*'], 'file');
            return (new ApiResponse(200, [$data], 'Upload File Fetched Successfully'))->send();
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return (new ApiResponse(404, [], 'File not found'))->send();
            }
            Log::error('Error fetching files: ' . $e->getMessage());
            return (new ApiResponse(500, [], 'Internal server error'))->send();
        }
    }

    public function store(Request $request)
    {
        $response = new ApiResponse();
        try {
            if (!$request->hasFile('file')) {
                return (new ApiResponse(400, [], 'File not provided'))->send();
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

            Log::info("File stored successfully at: {$path}");
            $uploadedFile = FileUpload::create([
                'url_id' => $uniqueName,
                'name' => $filename,
            ]);

            return response()->json($response->callResponse(201, $uploadedFile, 'File uploaded successfully'), 201);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json($response->callResponse(404, [], 'File not found'), 404);
            }
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
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json($response->callResponse(404, [], 'File not found'), 404);
            }
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
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json($response->callResponse(404, [], 'File not found'), 404);
            }
            Log::error('Error deleting file: ' . $e->getMessage());
            return response()->json($response->callResponse(500, [], 'Internal Server Error'), 500);
        }
    }
}
