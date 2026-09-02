<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{

    public function createSubscription(Request $request){
        $validation = Validator::make($request->all(), [
            'user_id'=>'required|exists:users,id',
            'plan_id'=>'required|exists:plans,id',
            'payment_method_id'=>'required|exists:payment_methods,id',
            'receipt'=>'nullable',
        ]);
        if($validation->fails()){
            return response()->json(['error'=>$validation->errors()],401);
        }

        $payments = Payment::create([
            'user_id'=>$request->user_id,
            'plan_id'=>$request->plan_id,
            'payment_method_id'=>$request->payment_method_id,
            'receipt'=>$request->receipt,
            'status'=>'pending'
        ]);
        return response()->json(['message'=>'subscription pending , wait for admin to approve']);
    }

    public function approvePayment($payment_id){
        $payment = Payment::findOrFail($payment_id);
        $payment->update(['status'=>'approved']);

        $user = User::find($payment->user_id);
        $user->plan_id = $payment->plan_id;
        $user->save();

        
        return response()->json(['message'=>'Subscription payment approved']);
    }

    public function getPendingPayments(){
        $payments = Payment::where('status','pending')
        ->get();
        $data = $payments->map(function ($payment) {
            return [
                'payment_id'=>$payment->id,
                'user_id'=>$payment->user_id,
                'user_name'=>$payment->user->first_name.' '.$payment->user->last_name,
                'plan_id'=>$payment->plan_id,
                'plan_name'=>$payment->plan->name,
                'plan_price'=>$payment->plan->price_after_discount,
                'payment_method_id'=>$payment->paymentmethod->id,
                'payment_method_name'=>$payment->paymentmethod->method_name,
                'receipt'=>$payment->receipt,
                'status'=>$payment->status,
            ];
        });
        return response()->json($data);
    }

    public function historyPayment(){
        $payments = Payment::where('status','approved')
        ->get();
        $data = $payments->map(function ($payment) {
            return [
                'payment_id'=>$payment->id,
                'user_id'=>$payment->user_id,
                'user_name'=>$payment->user->first_name.' '.$payment->user->last_name,
                'plan_id'=>$payment->plan_id,
                'plan_name'=>$payment->plan->name,
                'plan_price'=>$payment->plan->price_after_discount,
                'payment_method_id'=>$payment->paymentmethod->id,
                'payment_method_name'=>$payment->paymentmethod->method_name,
                'receipt'=>$payment->receipt,
                'status'=>$payment->status,
            ];
        });
        return response()->json(['history'=>$data]);
    }
}
