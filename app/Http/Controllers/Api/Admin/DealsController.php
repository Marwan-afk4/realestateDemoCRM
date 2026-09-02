<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\BrokerLead;
use App\Models\Compound;
use App\Models\Developer;
use App\Models\Lead;
use App\Models\SalesDeveloper;
use App\Models\Deal;
use App\Models\Uptown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $deals = Deal::findOrFail($id);
        $deals->status = 'semidone';
        $deals->save();

        return response()->json(['message' => 'Deal Updated Successfully']);
    }


    public function approveDeal($dealid, $developerId, $compoundid){

        $deal = Deal::findOrFail($dealid);
        $developer = Developer::findOrFail($developerId);
        $compound = Compound::findOrFail($compoundid);

        // Update Developer's deals done
        $developer->deals_done += 1;
        $developer->save();

        // Update Deal's Status
        $deal->status = 'approved';
        $deal->save();

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

        $deal->status = $request->status;
        $deal->save();
        return response()->json(['message'=>'Deal Status Updated Successfully']);

    }

    public function rejectdeal($id){
        $deal = Deal::findOrFail($id);
        $deal->status = 'rejected';
        $deal->save();
        return response()->json(['message'=>'Deal Rejected Successfully']);
    }

}

