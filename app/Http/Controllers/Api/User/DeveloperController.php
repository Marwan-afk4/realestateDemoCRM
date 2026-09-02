<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use App\Models\Developer;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{


    public function GetDevloper(){
        $developer = Developer::all();
        return response()->json(['developers'=>$developer]);
    }

    public function getCompounds($developer_id)
{
    $compounds = Compound::where('developer_id', $developer_id)
        ->with('uptwons.unitimages')
        ->get()
        ->map(function ($compound) {
            // Add the full URL directly to the image key
            $compound->uptwons->each(function ($uptown) {
                $uptown->unitimages->each(function ($image) {
                    // Replace 'admin/unit/images/' with the base URL of your images
                    $image->image = url('storage/' . $image->image); // Adjust the URL path as needed
                });
            });
            return $compound;
        });

    return response()->json(['compounds' => $compounds]);
}


}
