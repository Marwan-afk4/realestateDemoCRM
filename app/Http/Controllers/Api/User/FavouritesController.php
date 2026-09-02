<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use App\Models\Favourite;
use App\Models\Uptown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouritesController extends Controller
{

    public function getFavourites(){
        $units = Uptown::where('favourite', 1)->get();

        foreach ($units as $unit) {
            foreach ($unit->unitImages as $image) {
                $image->image = url('storage/' . $image->image);
            }
        }

        foreach ($units as $unit) {
            if($unit->master_plan_image){
                $unit->master_plan_image = url('storage/' . $unit->master_plan_image);
            }
        }

        foreach ($units as $unit) {
            if($unit->floor_plan_image){
                $unit->floor_plan_image = url('storage/' . $unit->floor_plan_image);
            }
        }


        $compounds = Compound::where('favourite', 1)->get();

        $data = [
            'units' => $units,
            'compounds' => $compounds
        ];
        return response()->json($data);
    }

    public function unitFavourite(Request $request,$id){
        $uptown = Uptown::find($id);
        $validation = Validator::make($request->all(), [
            'favourite' => 'required|between:0,1',
        ]);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }
        $uptown->update([
            'favourite' => $request->favourite
        ]);
        return response()->json(['message'=>'Unit Favourite Successfully']);
    }

    public function compoundFavourite(Request $request,$id){
        $compound = Compound::find($id);
        $validation = Validator::make($request->all(), [
            'favourite' => 'required|between:0,1',
        ]);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }
        $compound->update([
            'favourite' => $request->favourite
        ]);
        return response()->json(['message'=>'Compound Favourite Successfully']);
    }

}
