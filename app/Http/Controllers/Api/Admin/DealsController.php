<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\DealStatuses;
use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\BrokerLead;
use App\Models\Compound;
use App\Models\Developer;
use App\Models\Lead;
use App\Models\SalesDeveloper;
use App\Models\Deal;
use App\Models\Uptown;
use App\Services\Crm\DealCloser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DealsController extends Controller
{

    protected $updatePeriodDays=['days_for_profits'];

    public function getBrokerLeads($brokerId){

        $leads = Lead::where('brocker_id', $brokerId)->get();
        return response()->json(['leads'=>$leads]);
    }

    public function makeDeal(Request $request){
        $Validation = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'nationality_id' => 'nullable|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'developer_id' => 'required|exists:developers,id',
            'compound_id' => 'required|exists:compounds,id',
            'number_of_units' => 'required|integer|min:1',
        ]);

        if ($Validation->fails()) {
            return response()->json(['errors' => $Validation->errors()], 401);
        }

        $deal = Deal::create([
            'fullname' => $request->fullname,
            'nationality_id' => $request->nationality_id,
            'phone' => $request->phone,
            'email' => $request->email,
            'developer_id' => $request->developer_id,
            'compound_id' => $request->compound_id,
            'number_of_units' => $request->number_of_units,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Deal Added Successfully']);
    }




    public function getalldeals(){
        $deals = Deal::with('compound')->get();

        return response()->json(['deals'=>$deals]);
    }

    public function semidonedeal($id){

        $deal = Deal::findOrFail($id);
        try {
            app(DealCloser::class)->applyStatus($deal, DealStatuses::SemiDone);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        return response()->json(['message' => 'Deal Updated Successfully']);
    }


    public function approveDeal($dealid, $brokerId, $developerId, $unitId, $leadid, $compoundid){

        $deal = Deal::findOrFail($dealid);
        $developer = Developer::findOrFail($developerId);
        Compound::findOrFail($compoundid);

        $deal->forceFill([
            'brocker_id' => $brokerId ?: $deal->brocker_id,
            'developer_id' => $developerId,
            'uptown_id' => $unitId ?: $deal->uptown_id,
            'lead_id' => $leadid ?: $deal->lead_id,
            'compound_id' => $compoundid,
        ])->saveQuietly();

        $developer->deals_done += 1;
        $developer->save();

        try {
            app(DealCloser::class)->applyStatus($deal->fresh(), DealStatuses::Approved);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        return response()->json(['message' => 'Deal Approved Successfully']);

    }

    public function getleadbrockers(){
        $leadbrockers = BrokerLead::all();
        return response()->json(['leadbrockers'=>$leadbrockers]);
    }

    public function updateDealStatus(Request $request, $id){
        $deal = Deal::findOrFail($id);
        $validation = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected,semidone',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 422);
        }

        $status = DealStatuses::tryFrom($request->status);
        if (! $status) {
            return response()->json(['errors' => ['status' => ['Invalid status']]], 422);
        }

        try {
            app(DealCloser::class)->applyStatus($deal, $status);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        return response()->json(['message'=>'Deal Status Updated Successfully']);

    }

    public function rejectdeal($id){
        $deal = Deal::findOrFail($id);
        try {
            app(DealCloser::class)->applyStatus($deal, DealStatuses::Rejected);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        return response()->json(['message'=>'Deal Rejected Successfully']);
    }

}

