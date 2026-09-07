<?php

namespace App\Http\Controllers;

use App\Enums\SellRequestExecutionDate;
use App\Models\Compound;
use App\Models\Developer;
use App\Models\SellRequest;
use App\Models\UnitSubType;
use App\Models\Uptown;
use App\Models\UptownType;
use App\Models\UnitsImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

    public function create()
    {
        return view('sell-requests.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'age' => 'required|integer|min:18',
            'identity_front_image' => 'required|image|max:5120',
            'identity_back_image' => 'required|image|max:5120',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'developer_id' => 'nullable|exists:developers,id',
            'compound_id' => 'nullable|exists:compounds,id',
            'detailed_pdf' => 'required|file|mimes:pdf|max:10240',
            'price' => 'required|numeric|min:0',
            'installments' => 'required|boolean',
            'installments_years' => 'required_if:installments,1,true|nullable|integer|min:1',
            'installments_years_left' => 'required_if:installments,1,true|nullable|integer|min:0',
            'installments_total_price' => 'required_if:installments,1,true|nullable|numeric|min:0',
            'installments_price_per_year' => 'required_if:installments,1,true|nullable|numeric|min:0',
            'uptown_type_id' => 'required|exists:uptown_types,id',
            'unit_sub_type_id' => 'required|exists:unit_sub_types,id',
            'rooms_no' => 'nullable|integer|min:1',
            'bathrooms_no' => 'nullable|integer|min:1',
            'space' => 'nullable|numeric|min:1',
            'floor_no' => 'nullable|integer',
            'garden_area' => 'nullable|boolean',
            'garden_space' => 'nullable|numeric|min:0',
            'finishing' => 'required|in:finished,semi_finished,unfinished',
            'notes' => 'nullable|string',
            'execution_date' => ['required', Rule::enum(SellRequestExecutionDate::class)],
        ]);

        $data['identity_front_image'] = $request->file('identity_front_image')->store('sell_requests/identity', 'public');
        $data['identity_back_image'] = $request->file('identity_back_image')->store('sell_requests/identity', 'public');
        $data['detailed_pdf'] = $request->file('detailed_pdf')->store('sell_requests/pdfs', 'public');
        $data['installments'] = $request->boolean('installments');
        $data['garden_area'] = $request->boolean('garden_area');
        $data['status'] = 'pending';
        $data['visibility'] = 'private';

        if (! $data['installments']) {
            $data['installments_years'] = null;
            $data['installments_years_left'] = null;
            $data['installments_total_price'] = null;
            $data['installments_price_per_year'] = null;
        }

        $sellRequest = SellRequest::create($data);

        return redirect()->route('sell-requests.show', $sellRequest)
            ->with('success', __('Unit request created successfully'));
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

    private function formData(): array
    {
        $nameColumn = app()->getLocale() === 'ar' ? 'name_ar' : 'name_en';

        return [
            'users' => User::query()->orderBy('first_name')->get()
                ->mapWithKeys(fn (User $user) => [$user->id => trim($user->full_name).' ('.$user->phone.')'])
                ->all(),
            'developers' => Developer::orderBy($nameColumn)->pluck($nameColumn, 'id')->all(),
            'compounds' => Compound::orderBy('compound_name')->pluck('compound_name', 'id')->all(),
            'uptownTypes' => UptownType::orderBy($nameColumn)->pluck($nameColumn, 'id')->all(),
            'unitSubTypes' => UnitSubType::orderBy($nameColumn)->get()
                ->mapWithKeys(fn (UnitSubType $type) => [$type->id => $type->{$nameColumn} ?: $type->name_en])
                ->all(),
            'executionDates' => SellRequestExecutionDate::labels(),
            'finishings' => [
                'finished' => __('Finished'),
                'semi_finished' => __('Semi finished'),
                'unfinished' => __('Unfinished'),
            ],
        ];
    }
}
