<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\TrainingSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestsController extends Controller
{


    //training
    public function sendTrainingRequest(Request $request){
        $user = $request->user();
        $validation = Validator::make($request->all(), [
            'full_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|integer',
            'age' => 'required|integer',
            'qualification' => 'nullable|string',
            'governate' => 'nullable|string',
            'experience_year' => 'nullable|integer',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()], 422);
        }
        $new_training = TrainingSubscription::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'age' => $request->age,
            'qualification' => $request->qualification,
            'governate' => $request->governate,
            'experience_year' => $request->experience_year,
        ]);

        return response()->json(['message' => 'Training Request Sent Successfully']);
    }

//complaint

    public function sendComplaint(Request $request){
        $user = $request->user();
        $validation = Validator::make($request->all(), [
            'name'=>'required',
            'phone' => 'required|integer',
            'message' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()], 422);
        }
        $new_complaint = Complaint::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'message' => $request->message,
        ]);

        return response()->json(['message' => 'Complaint Sent Successfully']);
    }
}
