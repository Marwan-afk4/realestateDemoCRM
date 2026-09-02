<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required_without:fcm_token|string|max:512',
            'fcm_token' => 'required_without:token|string|max:512',
            'platform' => 'nullable|string|in:android,ios,web',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 422);
        }

        $token = $request->input('token') ?? $request->input('fcm_token');

        DeviceToken::register($request->user(), $token, $request->input('platform'));

        return response()->json([
            'message' => 'Device token registered',
        ]);
    }

    public function destroy(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required_without:fcm_token|string',
            'fcm_token' => 'required_without:token|string',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 422);
        }

        $token = $request->input('token') ?? $request->input('fcm_token');

        DeviceToken::where('user_id', $request->user()->id)
            ->where('token', $token)
            ->delete();

        return response()->json([
            'message' => 'Device token removed',
        ]);
    }
}
