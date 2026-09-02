<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\UseUptown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UsesVedioController extends Controller
{

    public function getVedios()
    {

        $videos = UseUptown::all();
        return response()->json(['videos' => $videos]);
    }

    public function uploadVideo(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|max:404800',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        if ($request->file('video')->isValid()) {
            $video = $request->file('video');
            $video_name = $request->name . '_' . $video->getClientOriginalName();
            $vedio_path = $video->storeAs('videos_How_to_use', $video_name, 'public');

            $videoModel = new UseUptown();
            $videoModel->name = $request->name;
            $videoModel->title = $request->title;
            $videoModel->description = $request->description;
            $videoModel->vedio_path = asset('storage/' . $vedio_path);
            $videoModel->save();

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully.',
                'data' => [
                    'id' => $videoModel->id,
                    'title' => $videoModel->title,
                    'description' => $videoModel->description,
                    'video_url' => asset('storage/' . $vedio_path),
                ]
            ], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Video upload failed.'], 500);
        }
    }



    public function deleteVideo($id)
    {
        $vedio = UseUptown::find($id);

        if (!$vedio) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $vedioPath = str_replace('storage/', '', $vedio->vedio_path);

        if (Storage::disk('public')->exists($vedioPath)) {
            Storage::disk('public')->delete($vedioPath);
        } else {
            return response()->json(['message' => 'Video file not found in storage'], 404);
        }

        // Delete the record from the database
        $vedio->delete();

        return response()->json(['message' => 'Video deleted successfully'], 200);
    }
}
