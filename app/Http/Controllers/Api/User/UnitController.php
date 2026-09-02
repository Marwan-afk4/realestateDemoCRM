<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Compound;
use App\Models\Uptown;

class UnitController extends Controller
{
    public function getUnits()
    {
        $uptowns = Uptown::with(['developer', 'compound', 'uptownType', 'unitimages'])->get();

        $data = [
            'units' => $uptowns,
        ];

        return response()->json($data);
    }


    public function getBuyUnits()
    {
        $uptowns = Uptown::with(['developer', 'compound', 'uptownType', 'unitimages'])->where('type', 'buy')->get();

        $data = [
            'units' => $uptowns,
        ];

        return response()->json($data);
    }

    public function getRentUnits()
    {
        $uptowns = Uptown::with(['developer', 'compound', 'uptownType', 'unitimages'])->where('type', 'rent')->get();

        $data = [
            'units' => $uptowns,
        ];

        return response()->json($data);
    }

    public function getCompoundswithCommission()
    {
        $compounds = Compound::all();

        $data = $compounds->map(function ($compound) {
            return [
                'id' => $compound->id,
                'compound_name' => $compound->compound_name,
                'commission_percentage' => $compound->commission_percentage,
                'compound_image' => $compound->image_url,
                'developer_id' => $compound->developer_id,
                'developer_name' => $compound->developer->name,
            ];
        });

        return response()->json(['compounds' => $data]);
    }
}
