<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\BrokerLead;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrokerController extends Controller
{


    public function addLeadtoBroker(Request $request, $id)
{
    $broker = Brocker::findOrFail($id);

    $validation = Validator::make($request->all(), [
        'lead_id' => 'required|array',
        'lead_id.*.lead_id' => 'required|exists:leads,id|unique:leads,brocker_id', // Ensures a lead is not already assigned to a broker
        'lead_id.*.brocker_end_date' => 'required|date',
    ]);

    if ($validation->fails()) {
        return response()->json(['errors' => $validation->errors()], 422);
    }

    // Count the number of leads being added
    $leadsAdded = count($request->lead_id);

    foreach ($request->lead_id as $leadData) {
        // Update the lead directly in the leads table
        Lead::findOrFail($leadData['lead_id'])->update([
            'brocker_id' => $broker->id,
            'brocker_end_date' => $leadData['brocker_end_date'],
            'status' => 'pending',
        ]);
        $broker_lead = BrokerLead::create([
            'brocker_id' => $broker->id,
            'brocker_end_date' => $leadData['brocker_end_date'],
            'lead_id' => $leadData['lead_id'],
            'status' => 'in_progress',
        ]);
    }

    // Update the broker's number of deals
    $broker->number_of_deals += $leadsAdded;
    $broker->save();

    return response()->json(['message' => 'Leads assigned successfully']);
}



}
