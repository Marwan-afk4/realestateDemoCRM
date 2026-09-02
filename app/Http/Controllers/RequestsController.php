<?php

namespace App\Http\Controllers;

use App\Models\Brocker;
use App\Models\Complaint;
use App\Models\Contract;
use App\Models\TrainingSubscription;
use App\Models\User;
use App\Models\Subscribtion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequestsController extends Controller
{
    public function index(Request $request)
    {
        $tab = 'complaints';
        $data = $this->getComplaints($request);

        return view('requests.index', compact('tab', 'data'));
    }

    private function updateExpiredTrainings()
    {
        $expiredTrainings = TrainingSubscription::where('status', 'approved')->get();

        foreach ($expiredTrainings as $training) {
            $expirationDate = $training->updated_at->addDays($training->training_period)->startOfDay();

            if (now()->startOfDay()->greaterThanOrEqualTo($expirationDate)) {
                $training->status = 'completed';
                $training->save();
            }
        }
    }

    private function getTrainingRequests(Request $request)
    {
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $keyword = $request->get('keyword');

        $query = TrainingSubscription::with('user')
            ->when($keyword, function ($q, $keyword) {
                $q->where(function ($query) use ($keyword) {
                    $query->where('full_name', 'LIKE', "%{$keyword}%")
                          ->orWhere('email', 'LIKE', "%{$keyword}%")
                          ->orWhere('phone', 'LIKE', "%{$keyword}%")
                          ->orWhere('qualification', 'LIKE', "%{$keyword}%");
                });
            });

        return $query->orderBy($sortField, $sortOrder)->paginate(15);
    }

    private function getComplaints(Request $request)
    {
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $keyword = $request->get('keyword');

        $query = Complaint::with('user')
            ->when($keyword, function ($q, $keyword) {
                $q->where(function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%")
                          ->orWhere('phone', 'LIKE', "%{$keyword}%")
                          ->orWhere('message', 'LIKE', "%{$keyword}%")
                          ->orWhereHas('user', function ($userQuery) use ($keyword) {
                              $userQuery->where('first_name', 'LIKE', "%{$keyword}%")
                                       ->orWhere('last_name', 'LIKE', "%{$keyword}%")
                                       ->orWhere('email', 'LIKE', "%{$keyword}%");
                          });
                });
            });

        return $query->orderBy($sortField, $sortOrder)->paginate(15);
    }


    // Training Actions
    public function approveTraining(Request $request, $id)
    {
        $request->validate([
            'training_period' => 'required|integer|min:1|max:365',
        ]);

        $training = TrainingSubscription::findOrFail($id);
        $training->update([
            'training_period' => $request->training_period,
            'status' => 'approved'
        ]);

        return redirect()->route('requests.index', ['tab' => 'training'])
                        ->with('success', __('Training request approved successfully'));
    }

    public function rejectTraining($id)
    {
        $training = TrainingSubscription::findOrFail($id);
        $training->update(['status' => 'rejected']);

        return redirect()->route('requests.index', ['tab' => 'training'])
                        ->with('success', __('Training request rejected'));
    }

    // Complaint Actions
    public function closeComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->update(['status' => 'closed']);

        return redirect()->route('requests.index', ['tab' => 'complaints'])
                        ->with('success', __('Complaint closed successfully'));
    }

}
