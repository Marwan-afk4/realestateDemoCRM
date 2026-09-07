<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSellRequest;
use App\Models\SellRequest;
use App\Models\UnitSubType;
use App\Models\Uptown;
use App\trait\image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SellRequestController extends Controller
{
    use image;

    /**
     * Get sub-types for a specific unit type.
     */
    public function getSubTypes($id)
    {
        $uptown_type_id = $id;

        $subTypes = UnitSubType::where('uptown_type_id', $uptown_type_id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $subTypes
        ]);
    }

    /**
     * Store a new sell request.
     */
    public function store(StoreSellRequest $request)
    {
        $data = $request->validated();
        
        // Handle file uploads
        if ($request->filled('identity_front_image')) {
            $data['identity_front_image'] = $this->storeBase64Image($request->identity_front_image, 'sell_requests/identity');
        }
        
        if ($request->filled('identity_back_image')) {
            $data['identity_back_image'] = $this->storeBase64Image($request->identity_back_image, 'sell_requests/identity');
        }
        
        if ($request->filled('detailed_pdf')) {
            $data['detailed_pdf'] = $this->storeBase64File($request->detailed_pdf, 'sell_requests/pdfs');
        }

        if ($request->filled('unit_plan')) {
            $data['unit_plan'] = $this->storeBase64Image($request->unit_plan, 'sell_requests/unit_plans');
        }

        // Multipart video file (not base64)
        unset($data['video']);
        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('sell_requests/videos', 'public');
        }

        // Set the authenticated user ID
        $data['user_id'] = auth()->id() ?? $request->user_id; // Fallback for testing

        $sellRequest = SellRequest::create($data);

        // Handle multiple unit images with keys
        if ($request->has('images') && is_array($request->images)) {
            foreach ($request->images as $key => $image) {
                $path = $this->storeBase64Image($image, 'sell_requests/units');
                $sellRequest->images()->create([
                    'key' => $key,
                    'image' => $path
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => __('Sell request submitted successfully'),
            'data' => $sellRequest
        ], 201);
    }

    public function index(Request $request)
    {
        $sellRequests = SellRequest::with(['uptownType', 'unitSubType', 'images'])
            ->where('user_id', auth()->id())
            ->get();
            
        return response()->json([
            'status' => 'success',
            'data' => $sellRequests
        ]);
    }

    public function show($id)
    {
        $sellRequest = SellRequest::with(['uptownType', 'unitSubType', 'images', 'developer', 'compound'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $sellRequest
        ]);
    }

    public function updateDeliveryDate(Request $request, $id)
    {
        abort_unless($request->user()?->can('view-uptowns'), 403);

        $validation = Validator::make($request->all(), [
            'delivery_date' => 'required|date',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validation->errors()
            ], 422);
        }

        $uptown = Uptown::findOrFail($id);

        $uptown->update([
            'delivery_date' => $request->delivery_date
        ]);

        return response()->json([
            'status' => 'success',
            'message' => ('Delivery date updated successfully'),
            'data' => $uptown
        ]);
    }
}
