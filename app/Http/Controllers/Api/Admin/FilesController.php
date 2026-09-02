<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FilesController extends Controller
{

    public function getallFiles()
    {
        $files = File::all();
        return response()->json(['files' => $files]);
    }

    public function addFile(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf',
            'name' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        if ($request->file('file')->isValid()) {
            $file = $request->file('file');
            $file_name = $request->name . '.' . $file->getClientOriginalExtension();
            $filepath = $file->storeAs('files', $file_name, 'public'); // Add 'public' to ensure it's stored in public-accessible disk

            $fileModel = new File();
            $fileModel->name = $file_name;
            $fileModel->path = asset('storage/' . $filepath);
            $fileModel->save();

            return response()->json([
                'success' => true,
                'message' => 'File Added Successfully',
                'data' => [
                    'id' => $fileModel->id,
                    'name' => $fileModel->name,
                    'url' => asset('storage/' . $filepath),
                ]
            ], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'File upload failed.'], 500);
        }
    }



    public function deleteFile($id){

        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $filePath = str_replace(asset('storage/'), '', $file->path);

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $file->delete();

        return response()->json(['message' => 'File deleted successfully'], 200);
    }
}
