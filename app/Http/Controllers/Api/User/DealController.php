<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use App\Models\Deal;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DealController extends Controller
{
    public function sendDeal(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'nationality_id' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'developer_id' => 'required|exists:developers,id',
            'compound_id' => 'required|exists:compounds,id',
            'uptown_type_id' => 'required|exists:uptown_types,id',
            'number_of_units' => 'required|integer|min:1',
        ]);

        if ($validation->fails()) {
            return response()->json(['message' => $validation->errors()], 422);
        }

        $new_deal = Deal::create([
            'fullname' => $request->fullname,
            'nationality_id' => $request->nationality_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'developer_id' => $request->developer_id,
            'compound_id' => $request->compound_id,
            'uptown_type_id' => $request->uptown_type_id,
            'number_of_units' => $request->number_of_units,
        ]);

        return response()->json(['message' => 'Deal Sent Successfully', 'deal_id' => $new_deal->id]);
    }

    public function getDeveloperIds()
    {
        $developers = Developer::all();
        $data = $developers->map(function ($developer) {
            return [
                'id' => $developer->id,
                'name' => $developer->name,
            ];
        });

        return response()->json(['developers' => $data]);
    }

    public function getCompoundIds($developer_id)
    {
        $compounds = Compound::where('developer_id', $developer_id)->get();
        $data = $compounds->map(function ($compound) {
            return [
                'id' => $compound->id,
                'compound_name' => $compound->compound_name,
            ];
        });

        return response()->json(['compounds' => $data]);
    }
}
