<?php
// app/Livewire/Facility/CongestionButton.php

namespace App\Livewire\Facility;

use Livewire\Component;
use App\Models\Tenant\Facility;
use App\Models\Tenant\FacilityCongestionLog;
use Illuminate\Support\Facades\Auth;

class CongestionButton extends Component
{
    public Facility $facility;
    public string $currentStatus;
    public string $lastUpdated;
    public bool $isOpen;

    public function mount(): void
    {
        $facilityId = Auth::user()->facility_id;
        $this->facility = Facility::findOrFail($facilityId);
        $this->currentStatus = $this->facility->congestion_status ?? 'unknown';
        $this->lastUpdated = $this->facility->congestion_updated_at
            ? $this->facility->congestion_updated_at->diffForHumans()
            : 'Never';
        $this->isOpen = $this->facility->isOpenNow();
    }

    public function updateStatus(string $status): void
    {
        if (!in_array($status, ['low', 'moderate', 'high', 'at_capacity'])) {
            return;
        }

        $this->facility->update([
            'congestion_status' => $status,
            'congestion_updated_at' => now(),
            'routing_priority' => $this->calculatePriority($status),
        ]);

        FacilityCongestionLog::create([
            'facility_id' => $this->facility->id,
            'status' => $status,
            'source' => 'manual',
            'reported_by' => Auth::id(),
        ]);

        $this->currentStatus = $status;
        $this->lastUpdated = 'just now';
        session()->flash('message', 'Congestion updated to ' . ucfirst($status) . '.');
    }

    protected function calculatePriority(string $status): int
    {
        return match ($status) {
            'low' => 10,
            'moderate' => 5,
            'high' => 1,
            'at_capacity' => -10,
            default => 0,
        };
    }

    public function render()
    {
        return view('livewire.facility.congestion-button')->layout('layouts.app');
    }
}