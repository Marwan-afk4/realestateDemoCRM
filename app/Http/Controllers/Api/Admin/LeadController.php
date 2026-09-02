<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\Lead;
use App\Models\MarketingAgency;
use App\Models\Uptown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{


    public function getleads(){
        $leads = Lead::all();
        $data = $leads->map(function ($lead) {
            return [
                'lead_id' => $lead->id,
                'lead_name' => $lead->lead_name,
                'lead_phone' => $lead->lead_phone,
                'lead_status' => $lead->status,
                'marketing_agency_id'=> $lead->marketing_agency_id,
                'marketing_agency_name' => $lead->marketing_agency->name??null,
                'sales_man_name' => $lead->sales_man_name,
                'sales_man_phone' => $lead->sales_man_phone
            ];
        });
        return response()->json(['leads'=>$data]);
    }

    public function deleteLead($id){
        $lead = Lead::find($id);
        $lead->delete();
        return response()->json(['message'=>'Lead deleted successfully']);
    }

    public function AddLead(Request $request){
        $validation = Validator::make($request->all(), [
            'marketing_agency_id' => 'required|exists:marketing_agencies,id',
            'interested_place'=>'nullable',
            'lead_name' => 'required',
            'lead_phone' => 'required',
            'sales_man_name' => 'nullable',
            'sales_man_phone' => 'nullable',
        ]);

        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 401);
        }
        $marketing_agency = MarketingAgency::find($request->marketing_agency_id);
        $lead = Lead::create([
            'marketing_agency_id' => $request->marketing_agency_id,
            'interested_place' => $request->interested_place ?? null,
            'lead_name' => $request->lead_name,
            'lead_phone' => $request->lead_phone,
            'sales_man_name' => $request->sales_man_name,
            'sales_man_phone' => $request->sales_man_phone
        ]);
        $marketing_agency->total_leads = $marketing_agency->total_leads + 1;
        $marketing_agency->save();
        return response()->json(['message'=>'Lead added successfully']);
    }

    public function getIDS(){
        $marketing_agencies = MarketingAgency::all();
        $brockers = Brocker::all();
        $data=[
            'marketing_agencies'=>$marketing_agencies,
        ];
        return response()->json($data);
    }
}
