<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingAgency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarketingAgencyController extends Controller
{

    public function getMarketingAgency(){

        $agencies = MarketingAgency::with('leads')->get();

        foreach ($agencies as $agency) {
            $actualLeadCount = count($agency->leads);
            $agency->total_leads = $actualLeadCount;
            $agency->save();
        }

        return response()->json(['Marketing_Agency' => $agencies]);
    }



    public function addMarketagency(Request $request){
        $validation = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>'nullable|email',
            'phone'=>'required',
            'start_date'=>'nullable|date',
            'end_date'=>'required|date',
            'image'=>'nullable',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }
        $agency=MarketingAgency::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'start_date'=>$request->start_date,
            'end_date'=>$request->end_date,
            'image'=>$request->image,
        ]);

        return response()->json(['message'=>'Marketing Agency Added Successfully']);
    }

    public function deleteMarketingAgency($id){
        $agency=MarketingAgency::find($id);
        $agency->delete();
        return response()->json(['message'=>'Marketing Agency Deleted Successfully']);
    }
}
