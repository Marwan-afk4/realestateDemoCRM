<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{


    public function getPaymentMethods(){
        $payemnt_methods = PaymentMethod::all();
        $data = [
            'payment_methods'=>$payemnt_methods
        ];
        return response()->json($data);
    }

    public function createPaymentMethod(Request $request){
        $validation = Validator::make($request->all(), [
            'method_name'=>'required',
            'image'=>'nullable',
        ]);
        if($validation->fails()){
            return response()->json(['error'=>$validation->errors()],401);
        }

        $payment_method = PaymentMethod::create([
            'method_name'=>$request->method_name,
            'image'=>$request->image,
            'status'=>'active'
        ]);
        return response()->json(['message'=>'Payment Method Added Successfully']);

    }

    public function deletePaymentMethod($id){
        $payment_method = PaymentMethod::find($id);
        $payment_method->delete();
        return response()->json(['message'=>'Payment Method Deleted Successfully']);
    }
}
