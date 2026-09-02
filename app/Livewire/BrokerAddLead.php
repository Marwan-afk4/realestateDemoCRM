<?php

namespace App\Livewire;

use App\Models\Brocker;
use App\Models\BrokerLead;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class BrokerAddLead extends Component
{
    public Brocker $brocker;

    public $lead_id = '';
    public $search = '';
    public $searchAssigned = '';

    public array $leads = [];
    public $availableLeads;
    public Collection $brokerLeads;

    public $temp_date;

    public function mount(Brocker $brocker)
    {
        $this->brocker = $brocker;
        $this->updateBrokerLeads();
        $this->updateAvailableLeads();
    }

    private function updateBrokerLeads()
    {
        $query = BrokerLead::where('brocker_id', $this->brocker->id)
            ->with('lead');

        if ($this->searchAssigned) {
            $query->whereHas('lead', function ($q) {
                $q->where('lead_name', 'like', '%' . $this->searchAssigned . '%')
                    ->orWhere('lead_phone', 'like', '%' . $this->searchAssigned . '%');
            });
        }

        $this->brokerLeads = $query->get();
    }

    private function updateAvailableLeads()
    {
        $query = Lead::whereNull('brocker_id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('lead_name', 'like', '%' . $this->search . '%')
                    ->orWhere('lead_phone', 'like', '%' . $this->search . '%');
            });
        }

        $this->availableLeads = $query->get();
        $this->leads = $this->availableLeads->pluck('lead_name', 'id')->toArray();
    }

    public function updatedSearch()
    {
        $this->updateAvailableLeads();
        $this->reset('lead_id');
    }

    public function updatedSearchAssigned()
    {
        $this->updateBrokerLeads();
    }

    public function assignLead()
    {
        $this->validate([
            'lead_id' => 'required|exists:leads,id|unique:leads,brocker_id',
        ]);

        $lead = Lead::findOrFail($this->lead_id);

        // update lead record
        $lead->update([
            'brocker_id' => $this->brocker->id,
            'brocker_end_date' => null, // No end date on assignment
            'status' => 'pending',
        ]);

        // add broker_lead record
        BrokerLead::create([
            'lead_id' => $this->lead_id,
            'brocker_id' => $this->brocker->id,
            'brocker_end_date' => null, // No end date on assignment
            'status' => 'in_progress',
        ]);

        $this->brocker->increment('number_of_deals');

        $this->reset(['lead_id']);
        $this->updateBrokerLeads();
        $this->updateAvailableLeads();

        session()->flash('success', 'Lead assigned successfully!');
    }

    public function updateEndDate($brokerLeadId, $newDate)
    {
        $brokerLead = BrokerLead::find($brokerLeadId);

        if ($brokerLead) {
            // Handle empty string as null
            $dateValue = empty($newDate) ? null : $newDate;

            try {
                $brokerLead->update(['brocker_end_date' => $dateValue]);
                $brokerLead->lead->update(['brocker_end_date' => $dateValue]);
                $this->updateBrokerLeads();

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

    public function updateLeadStatus($brokerLeadId, $newStatus)
    {
        $brokerLead = BrokerLead::find($brokerLeadId);

        if ($brokerLead) {
            try {
                $brokerLead->lead->update(['status' => $newStatus]);
                $this->updateBrokerLeads();
                session()->flash('success', 'Lead status updated successfully!');
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to update status');
            }
        }
    }

    public function unassignLead($brokerLeadId)
    {
        $brokerLead = BrokerLead::find($brokerLeadId);

        if ($brokerLead) {
            $lead = $brokerLead->lead;
            $lead->update([
                'brocker_id' => null,
                'brocker_end_date' => null,
                'status' => 'empty',
            ]);

            $brokerLead->delete();

            $this->brocker->decrement('number_of_deals');
            $this->updateBrokerLeads();
            $this->updateAvailableLeads();

            session()->flash('success', 'Lead unassigned successfully!');
        }
    }

    public function getCanAssignProperty()
    {
        return !empty($this->leads) && !empty($this->lead_id);
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->updateAvailableLeads();
    }

    public function clearAssignedSearch()
    {
        $this->searchAssigned = '';
        $this->updateBrokerLeads();
    }

    public function selectLead($leadId)
    {
        $this->lead_id = $leadId;
    }

    public function quickAssignLead($leadId)
    {
        $this->lead_id = $leadId;
        $this->assignLead();
    }

    public function render()
    {
        return view('livewire.broker-add-lead');
    }
}
