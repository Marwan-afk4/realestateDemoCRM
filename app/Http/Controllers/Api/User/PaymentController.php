<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{

    public function makePayment(Request $request){
        $user = $request->user();
        $validation = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'receipt' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()], 422);
        }
        $payments = Payment::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'payment_method_id' => $request->payment_method_id,
            'receipt' => $request->receipt,
            'status' => 'pending',
        ]);
        return response()->json(['message' => 'Payment made successfully , wait for the admin to approve']);
    }



}
