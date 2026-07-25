<?php

namespace App\Livewire\Verification;

use App\Models\Tenant;
use App\Models\Tenant\Facility;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FacilityReviewDetail extends Component
{
    public Facility $facility;

    public string $verificationNotes = '';

    public bool $showApproveConfirm = false;

    public bool $showRejectConfirm = false;

    public ?string $facilityTenantId = null;   // <-- add this

    protected function switchToFacilityTenant(): void
    {
        if ($this->facilityTenantId) {
            $database = 'nafasi_tenant_'.$this->facilityTenantId;
            config(['database.connections.tenant.database' => $database]);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }
    }



public function mount($facilityId)   // accept the ID, not the model
{
    if (!auth()->user()->can('facility.verify')) {
        abort(403);
    }
    $this->facility = Facility::findOrFail($facilityId);   // loads from current tenant DB
    $this->verificationNotes = $this->facility->verification_notes ?? '';
}

public function markUnderReview()
{
    $this->switchToFacilityTenant();
    $this->facility->update([
        'registration_status' => 'under_review',
        'verified_by' => auth()->id(),
    ]);
    session()->flash('message', 'Facility marked as under review.');
}

public function approve()
{
    $this->switchToFacilityTenant();
    $this->facility->update([
        'registration_status' => 'approved',
        'is_verified' => true,
        'verified_by' => auth()->id(),
        'verified_at' => now(),
        'verification_notes' => $this->verificationNotes,
        'subscription_status' => 'active',
    ]);

    $this->showApproveConfirm = false;
    session()->flash('message', 'Facility approved and is now live.');
    return redirect()->route('verification.queue');
}

public function reject()
{
    $this->switchToFacilityTenant();
    $this->facility->update([
        'registration_status' => 'rejected',
        'verified_by' => auth()->id(),
        'verification_notes' => $this->verificationNotes,
    ]);

    $this->showRejectConfirm = false;
    session()->flash('message', 'Facility rejected.');
    return redirect()->route('verification.queue');
}

    public function render()
    {
        return view('livewire.verification.facility-review-detail', [
            'facilityTypes' => Facility::facilityTypes(),
            'availableCapabilities' => Facility::availableCapabilities(),
        ])->layout('layouts.app');
    }
}
