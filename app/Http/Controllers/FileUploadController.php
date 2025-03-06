<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileUploadRequest;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use App\Http\Controllers\findOrFail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileUploadController extends Controller
{
   
     
    public function index()
    {
        try{
        $data = FileUpload::all();

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Success get all images',
            'data' => $data
        ]);
        } catch(\Exception $e) {
            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Internal Server Error'
            ]);
        }
    }
    public function store(FileUploadRequest $request)
    {
        try {
            $file = $request->file('file');
    
            if (!$file) {
                return response()->json([
                    'status' => false,
                    'status_code' => 400,
                    'message' => 'No file uploaded'
                ], 400);
            }
    
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueName = uniqid() . '-' . $filename . '.' . $extension;
    
            $path = $file->storeAs('uploads', $uniqueName, 'public');
    
            $uploadedFile = FileUpload::create([
                'url_id' => $uniqueName,
                'name' => $filename,
            ]);
    
            return response()->json([
                'status' => true,
                'status_code' => 200,
                'message' => 'Success upload file',
                'data' => $uploadedFile
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage()); 
            return response()->json([
                'status' => false,
                'status_code' => 500,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function show($id)
    {
    try {
        $file = FileUpload::findOrFail($id);

        $fileUrl = Storage::url('uploads/' . $file->url_id);

        if (!Storage::disk('public')->exists('uploads/' . $file->url_id)) {
            return response()->json([
                'status' => false,
                'status_code' => 404,
                'message' => 'File not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'Success get file',
            'data' => [
                'file' => $file,
                'file_url' => $fileUrl, // Returning the URL of the file
            ]
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Handle case where file is not found
        return response()->json([
            'status' => false,
            'status_code' => 404,
            'message' => 'File not found',
        ], 404);
    }
}



    public function update(Request $request, FileUpload $file)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $file->update(['name' => $request->name]);

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'File updated successfully',
            'data' => $file
        ]);
    }

  
    public function destroy(FileUpload $file)
    {
        if (Storage::disk('public')->exists('uploads/' . $file->url_id)) {
            Storage::disk('public')->delete('uploads/' . $file->url_id);
        }

        $file->delete();

        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'File deleted successfully'
        ]);
    }
}
