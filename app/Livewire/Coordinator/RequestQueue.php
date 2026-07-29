<?php
// app/Livewire/Coordinator/RequestQueue.php

namespace App\Livewire\Coordinator;

use App\Models\Tenant;
use App\Models\Tenant\AssistanceRequest;
use App\Models\Tenant\Facility;
use Livewire\Component;

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

        public function ensureTenant(): void
    {
        if (tenancy()->initialized) {
            return;
        }

        $host = request()->getHost();

        // This component is tenant-only. Never attempt resolution on the
        // central domain — CentralCommunityAlertFeed is what renders there.
        if (in_array($host, config('tenancy.central_domains', []), true)) {
            return;
        }

        $tenant = Tenant::whereHas('domains', fn ($q) => $q->where('domain', $host))->first();

        if ($tenant) {
            tenancy()->initialize($tenant);
        }
    }

    public function refreshQueue()
    {
         $this->ensureTenant();
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