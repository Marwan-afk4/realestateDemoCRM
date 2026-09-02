<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Plan;

class PyamentMethodsController extends Controller
{


    public function PlansPaymentMethod(){
        $plans = Plan::all();
        $payment_methods = PaymentMethod::all();
        $data = [
            'plans' => $plans,
            'payment_methods' => $payment_methods
        ];
        return response()->json($data);
    }

}
