<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use App\Models\Developer;
use App\Models\Uptown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompundController extends Controller
{


    public function compounds(){
        // Get all compounds
        $compounds = Compound::all();

        // Add the count of unsold units for each compound
        foreach ($compounds as $compound) {
            // Get the count of unsold units for the current compound
            $unsoldUnitsCount = Uptown::where('compound_id', $compound->id)
            ->where('status', 'unsold')
            ->count();

            $compound->unsold_units_count = $unsoldUnitsCount;
        }

        // Prepare the data to return
        $data = [
            'compounds' => $compounds
        ];

        return response()->json($data);
    }


    public function getdevelopers(){
        $developers = Developer::all();
        $data = [
            'developers' => $developers
        ];
        return response()->json($data);
    }

    public function addCompound(Request $request){
        $validation = Validator::make($request->all(), [
            'developer_id' => 'required|exists:developers,id',
            'compound_name' => 'required|unique:compounds,compound_name',
            'commission_percentage' => 'required',
            'image' => 'nullable',
        ]);
        if($validation->fails()){
            return response()->json(['error'=>$validation->errors()],401);
        }

        $compound = Compound::create([
            'developer_id'=>$request->developer_id,
            'compound_name'=>$request->compound_name,
            'commission_percentage'=>$request->commission_percentage,
            'image'=>$request->image ?? 'defualt.jpg',
        ]);
        return response()->json(['message'=>'Compound Added Successfully']);
    }

    public function deleteCompound($id){
        $compound = Compound::find($id);
        $compound->delete();
        return response()->json(['message'=>'Compound Deleted Successfully']);
    }


}
