<?php
// app/Livewire/Coordinator/RequestQueue.php

namespace App\Livewire\Coordinator;

use Livewire\Component;
use App\Models\Tenant\AssistanceRequest;
use App\Models\Tenant\Facility;

class RequestQueue extends Component
{
    public $pendingRequests = [];
    public $activeRequest = null;
    public $coordinatorNotes = '';
    public $dispatchFacilityId = null;
    public $dispatchMessage = '';
    public $estimatedArrival = '';

    public function mount()
    {
        $this->refreshQueue();
    }

    public function refreshQueue()
    {
        $this->pendingRequests = AssistanceRequest::with('dispatchedFacility')
            ->where('status', 'pending')
            ->orderByRaw("CASE WHEN urgency = 'emergency' THEN 1 WHEN urgency = 'urgent' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    public function acceptRequest($requestId)
    {
            if (!auth()->user()->can('coordinator.requests.handle')) {
        abort(403);
    }

    $request = AssistanceRequest::findOrFail($requestId);
        
        $request->update([
            'coordinator_id' => auth()->id(),
            'status' => 'accepted',
        ]);

        $this->activeRequest = $request->fresh()->toArray();
        $this->refreshQueue();
    }

    public function dispatchService()
    {
        if (!$this->activeRequest) return;

        $request = AssistanceRequest::findOrFail($this->activeRequest['id']);
        
        $request->update([
            'status' => 'dispatching',
            'dispatched_facility_id' => $this->dispatchFacilityId,
            'dispatch_message' => $this->dispatchMessage,
            'estimated_arrival' => $this->estimatedArrival,
            'dispatched_at' => now(),
            'coordinator_notes' => $this->coordinatorNotes,
        ]);

        $this->activeRequest = $request->fresh()->toArray();
    }

    public function resolveRequest($outcome = 'resolved')
    {
        if (!$this->activeRequest) return;

        $request = AssistanceRequest::findOrFail($this->activeRequest['id']);
        
        $request->update([
            'status' => 'resolved',
            'resolution' => $outcome,
            'resolved_at' => now(),
        ]);

        $this->activeRequest = null;
        $this->reset(['coordinatorNotes', 'dispatchFacilityId', 'dispatchMessage', 'estimatedArrival']);
        $this->refreshQueue();
    }

    public function cancelRequest()
    {
        if (!$this->activeRequest) return;

        $request = AssistanceRequest::findOrFail($this->activeRequest['id']);
        $request->update(['status' => 'cancelled']);

        $this->activeRequest = null;
        $this->refreshQueue();
    }

    public function render()
    {
        $dispatchFacilities = Facility::where('can_dispatch_to_patient', true)
            ->where('is_active', true)
            ->get();

        return view('livewire.coordinator.request-queue', [
            'dispatchFacilities' => $dispatchFacilities,
        ])->layout('layouts.app');
    }
}