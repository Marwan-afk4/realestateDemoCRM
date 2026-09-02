<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddLeadController extends Controller
{


    public function addLead(Request $request){
        $user = $request->user();
        $brocker = Brocker::where('user_id', $user->id)->first();
        $validator = Validator::make($request->all(), [
            'interested_place' => 'nullable',
            'lead_name' => 'required',
            'lead_phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 401);
        }
        $lead = Lead::create([
            'interested_place' => $request->interested_place ?? null,
            'lead_name' => $request->lead_name,
            'lead_phone' => $request->lead_phone,
            'brocker_id' => $brocker->id,
        ]);
        return response()->json(['message' => 'Lead added successfully']);
    }
}
