<?php

namespace App\Livewire;

use App\Models\Lead;
use App\Models\Brocker;
use App\Models\BrokerLead;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class LeadAssignBroker extends Component
{
    public Lead $lead;

    public $brocker_id = '';
    public $search = '';
    public $searchAssigned = '';
    public $currentBrokerId = null; // Track current broker assignment
    public $hasActiveAssignment = false; // Track assignment status
    public $canAssign = false; // Track if assignment is possible

    public $availableBrokers;
    public Collection $leadBrokers;

    public function mount(Lead $lead)
    {
        $this->lead = $lead;
        $this->currentBrokerId = $lead->brocker_id;
        $this->hasActiveAssignment = !empty($lead->brocker_id);
        $this->updateCanAssign();
        $this->updateLeadBrokers();
        $this->updateAvailableBrokers();
    }

    private function updateLeadBrokers()
    {
        $query = BrokerLead::where('lead_id', $this->lead->id)
            ->with('brocker.user')
            ->orderBy('created_at', 'desc');

        if ($this->searchAssigned) {
            $query->whereHas('brocker.user', function ($q) {
                $q->where('first_name', 'like', '%' . $this->searchAssigned . '%')
                    ->orWhere('last_name', 'like', '%' . $this->searchAssigned . '%')
                    ->orWhere('email', 'like', '%' . $this->searchAssigned . '%')
                    ->orWhere('phone', 'like', '%' . $this->searchAssigned . '%');
            });
        }

        $this->leadBrokers = $query->get();
    }

    private function updateAvailableBrokers()
    {
        $query = Brocker::with('user');

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $this->availableBrokers = $query->get();
    }

    public function updatedSearch()
    {
        $this->updateAvailableBrokers();
        $this->reset('brocker_id');
        $this->updateCanAssign();
    }

    public function updatedSearchAssigned()
    {
        $this->updateLeadBrokers();
    }

    public function updatedBrockerId()
    {
        $this->updateCanAssign();
    }



    public function assignBroker()
    {
        $this->validate([
            'brocker_id' => 'required|exists:brockers,id',
        ]);

        if ($this->hasActiveAssignment) {
            session()->flash('error', 'This lead already has an assigned broker. Please remove the current assignment first.');
            return;
        }

        // Update lead record
        $this->lead->update([
            'brocker_id' => $this->brocker_id,
            'brocker_start_date' => now(),
            'brocker_end_date' => null,
            'status' => 'pending',
        ]);

        // Add broker_lead record
        BrokerLead::create([
            'lead_id' => $this->lead->id,
            'brocker_id' => $this->brocker_id,
            'brocker_end_date' => null,
            'status' => 'in_progress',
        ]);

        // Refresh the lead model to update reactive properties
        $this->lead->refresh();
        $this->currentBrokerId = $this->lead->brocker_id;
        $this->hasActiveAssignment = !empty($this->lead->brocker_id);

        $this->reset(['brocker_id']);
        $this->updateLeadBrokers();
        $this->updateAvailableBrokers();
        $this->updateCanAssign();



        session()->flash('success', 'Broker assigned successfully!');
    }

    public function updateEndDate($brokerLeadId, $newDate)
    {
        $brokerLead = BrokerLead::find($brokerLeadId);

        if ($brokerLead) {
            $dateValue = empty($newDate) ? null : $newDate;

            try {
                $brokerLead->update(['brocker_end_date' => $dateValue]);

                // If this is the current broker, update the lead as well
                if ($this->lead->brocker_id == $brokerLead->brocker_id) {
                    $this->lead->update(['brocker_end_date' => $dateValue]);
                }

                $this->updateLeadBrokers();

                if ($dateValue) {
                    session()->flash('success', 'End date updated successfully!');
                } else {
                    session()->flash('success', 'End date removed successfully!');
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Invalid date format');
            }
        }
    }

    public function updateLeadStatus($newStatus)
    {
        try {
            $this->lead->update(['status' => $newStatus]);
            $this->lead->refresh();
            $this->updateLeadBrokers();
            session()->flash('success', 'Lead status updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update status');
        }
    }

    public function removeAssignment($brokerLeadId)
    {
        $brokerLead = BrokerLead::find($brokerLeadId);

        if ($brokerLead) {
            // If this is the current active assignment on the lead
            if ($this->lead->brocker_id == $brokerLead->brocker_id) {
                $this->lead->update([
                    'brocker_id' => null,
                    'brocker_end_date' => null,
                    'status' => 'empty',
                ]);
            }

            $brokerLead->delete();

            // Refresh the lead model to update reactive properties
            $this->lead->refresh();
            $this->currentBrokerId = $this->lead->brocker_id;
            $this->hasActiveAssignment = !empty($this->lead->brocker_id);

            $this->updateLeadBrokers();
            $this->updateAvailableBrokers();
            $this->updateCanAssign();



            session()->flash('success', 'Assignment removed successfully!');
        }
    }

    private function updateCanAssign()
    {
        $this->canAssign = !empty($this->brocker_id) && !$this->hasActiveAssignment;
    }

    public function selectBroker($brokerId)
    {
        $this->brocker_id = $brokerId;
    }

    public function quickAssignBroker($brokerId)
    {
        $this->brocker_id = $brokerId;
        $this->assignBroker();
    }

    public function render()
    {
        return view('livewire.lead-assign-broker');
    }
}
