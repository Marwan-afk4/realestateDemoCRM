<?php

namespace App\Http\Controllers;

use App\Models\SellRequest;
use App\Models\Uptown;
use App\Models\UnitsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SellRequestController extends Controller
{
    /**
     * Display a listing of the sell requests.
     */
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'id');
        $sortOrder = $request->get('order', 'DESC');
        $keyword = $request->get('keyword');

        $sellRequests = SellRequest::with(['user', 'uptownType', 'unitSubType'])
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('user', function($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', "%{$keyword}%")
                      ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                      ->orWhere('phone', 'LIKE', "%{$keyword}%");
                })
                ->orWhere('city', 'LIKE', "%{$keyword}%")
                ->orWhere('area', 'LIKE', "%{$keyword}%");
            })
            ->orderBy($sortField, $sortOrder)
            ->paginate(30);

        return view('sell-requests.index', compact('sellRequests', 'sortField', 'sortOrder'));
    }

    /**
     * Display the specified sell request.
     */
    public function show(SellRequest $sellRequest)
    {
        $sellRequest->load(['user', 'developer', 'compound', 'uptownType', 'unitSubType', 'images']);
        return view('sell-requests.show', compact('sellRequest'));
    }

    /**
     * Update the status of the sell request (optional logic).
     * When marking as contacted (accepted), admin must provide delivery_date.
     */
    public function updateStatus(Request $request, SellRequest $sellRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,contacted,approved,rejected',
            'delivery_date' => 'required_if:status,contacted|nullable|date_format:Y-m',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'contacted' && $request->filled('delivery_date')) {
            $data['delivery_date'] = $request->delivery_date; // YYYY-MM
        }

        $sellRequest->update($data);

        return back()->with('success', __('Status updated successfully'));
    }

    /**
     * Update the visibility of the sell request.
     */
    public function updateVisibility(Request $request, SellRequest $sellRequest)
    {
        $validation = Validator::make($request->all(), [
            'visibility' => 'required|in:public,private',
            'type' => 'required_if:visibility,public|in:buy,rent',
        ]);

        if ($validation->fails()) {
            return back()->with('error', $validation->errors()->first());
        }

        if ($request->visibility === 'public' && $sellRequest->visibility === 'private') {
            $deliveryDate = $sellRequest->deliveryDateAsFullDate();
            if (empty($deliveryDate)) {
                return back()->with('error', __('Cannot publish: Please accept the request and set a delivery date first.'));
            }

            try {
                // Create Uptown
                $uptown = Uptown::create([
                    'code' => 'UN-' . strtoupper(Str::random(8)),
                    'uptown_type_id' => $sellRequest->uptown_type_id,
                    'developer_id' => $sellRequest->developer_id,
                    'compound_id' => $sellRequest->compound_id,
                    'description' => $sellRequest->notes,
                    'name' => ($sellRequest->country ?? '') . ' , ' . $sellRequest->area,
                    'space' => $sellRequest->space,
                    'bathroom' => $sellRequest->bathrooms_no,
                    'bed' => $sellRequest->rooms_no,
                    'strat_price' => $sellRequest->price,
                    'status' => 'available',
                    'delivery_date' => $deliveryDate,
                    'cash' => $sellRequest->installments ? '0' : '1',
                    'installment' => $sellRequest->installments ? '1' : '0',
                    'installment_years' => $sellRequest->installments_years,
                    'installment_price' => $sellRequest->installments_total_price,
                    'type' => $request->type ?? 'buy',
                    'garden_space' => $sellRequest->garden_space,
                    'unit_plan' => $sellRequest->getRawOriginal('unit_plan'),
                ]);

                // Copy images
                foreach ($sellRequest->images as $srImage) {
                    UnitsImage::create([
                        'uptown_id' => $uptown->id,
                        'image' => $srImage->image,
                    ]);
                }
            } catch (\Exception $e) {
                return back()->with('error', __('Failed to create property listing: ') . $e->getMessage());
            }
        }

        $sellRequest->update(['visibility' => $request->visibility]);

        return back()->with('success', __('Visibility updated successfully'));
    }

    public function destroy(SellRequest $sellRequest)
    {
        $sellRequest->delete();
        return redirect()->route('sell-requests.index')->with('success', __('Sell request deleted successfully'));
    }
}
