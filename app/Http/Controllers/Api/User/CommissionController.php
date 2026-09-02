<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use Illuminate\Http\Request;

class CommissionController extends Controller
{


    public function getAllCommission(){
        $compounds = Compound::all();
        $data = $compounds->map(function ($compound) {
            return [
                'id' => $compound->id,
                'compound_name' => $compound->compound_name,
                'commission' => $compound->commission_percentage,
                'developer_id' => $compound->developer_id
            ];
        });
        return response()->json(['compounds_commission'=>$data]);
    }
}
