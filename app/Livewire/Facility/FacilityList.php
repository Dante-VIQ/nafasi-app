<?php
// app/Livewire/Facility/FacilityList.php

namespace App\Livewire\Facility;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant\Facility;

class FacilityList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterType = '';
    public string $filterStatus = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    protected $queryString = ['search', 'filterType', 'filterStatus'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive(int $facilityId): void
    {
        $facility = Facility::findOrFail($facilityId);
        $facility->update(['is_active' => !$facility->is_active]);
        session()->flash('message', $facility->name . ' ' . ($facility->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function render()
    {
        $facilities = Facility::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('city', 'like', "%{$this->search}%")
                      ->orWhere('county', 'like', "%{$this->search}%")
                      ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('facility_type', $this->filterType);
            })
            ->when($this->filterStatus !== '', function ($query) {
                if ($this->filterStatus === 'active') {
                    $query->where('is_active', true);
                } elseif ($this->filterStatus === 'inactive') {
                    $query->where('is_active', false);
                } elseif ($this->filterStatus === 'verified') {
                    $query->where('is_verified', true);
                } elseif ($this->filterStatus === 'unverified') {
                    $query->where('is_verified', false);
                }
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

        return view('livewire.facility.facility-list', [
            'facilities' => $facilities,
            'facilityTypes' => Facility::facilityTypes(),
        ])->layout('layouts.app');
    }
}