<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\HomeName;
use Illuminate\Http\Request;

class HomeNamesController extends Controller
{
    public function index()
    {
        $homeNames = HomeName::all();
        return response()->json([
            'status' => 'success',
            'data' => $homeNames
        ]);
    }
}
