<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brocker;
use App\Models\Complaint;
use App\Models\TrainingSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestsController extends Controller
{

//training contract
public function getTrainingRequests()
{
    $expiredTrainings = TrainingSubscription::where('status', 'approved')->get();

    foreach ($expiredTrainings as $training) {
        $expirationDate = $training->updated_at->addDays($training->training_period)->startOfDay();

        // Check if the training period has expired by comparing only the date part
        if (now()->startOfDay()->greaterThanOrEqualTo($expirationDate)) {
            $training->status = 'completed';
            $training->save();
        }
    }

    $pendingTrainings = TrainingSubscription::where('status', 'pending')->get();

    return response()->json([
        'pending_trainings' => $pendingTrainings,
    ]);
}




    public function acceptTrainer(Request $request,$id){
        $trainer = TrainingSubscription::findOrFail($id);
        $validarion = Validator::make($request->all(), [
            'training_period' => 'nullable|integer',
        ]);

        if ($validarion->fails()) {
            return response()->json(['errors' => $validarion->errors()], 422);
        }

        $trainer->training_period = $request->training_period;
        $trainer->status = 'approved';
        $trainer->save();
        return response()->json([
            'message' => 'Trainer accepted successfully',
        ]);
    }

//complaint

    public function getComplaints(){
        $complaints = Complaint::with('user:id,first_name,last_name,email,phone')
        ->where('status','open')
        ->get();
        return response()->json(['complaints'=>$complaints]);
    }

    public function closeComplaint($id){
        $complaint = Complaint::findOrFail($id);
        $complaint->status = 'closed';
        $complaint->save();
        return response()->json(['message'=>'Complaint Closed Successfully']);
    }

// lsa el ba2y leads and transactions
}
