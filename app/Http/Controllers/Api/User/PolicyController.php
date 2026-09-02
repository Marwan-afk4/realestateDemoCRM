<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index()
    {
        $policies = Policy::all();
        return response()->json([
            'status' => 'success',
            'policies' => $policies
        ]);
    }

    public function show($id)
    {
        $policy = Policy::find($id);
        if (!$policy) {
            return response()->json([
                'status' => 'error',
                'message' => 'Policy not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'policy' => $policy
        ]);
    }
}
