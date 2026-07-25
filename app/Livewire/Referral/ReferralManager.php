<?php
// app/Livewire/Referral/ReferralManager.php

namespace App\Livewire\Referral;

use Livewire\Component;
use App\Models\Tenant\Facility;
use App\Models\Tenant\Referral;
use App\Services\Referral\ReferralRouter;

class ReferralManager extends Component
{
    public ?Facility $facility = null;
    public array $referrals = [];
    public string $tab = 'incoming'; // incoming, outgoing
    public ?Referral $selectedReferral = null;
    public string $rejectionReason = '';
    public bool $showRejectModal = false;

    // Create referral form
    public bool $showCreateForm = false;
    public string $referral_type = '';
    public string $urgency = 'routine';
    public string $reason_for_referral = '';
    public string $clinical_summary = '';
    public string $patient_age_group = '';
    public string $patient_gender = '';
    public bool $patient_is_stable = true;
    public bool $requires_ambulance = false;
    public ?int $receiving_facility_id = null;
    public array $suggestedFacilities = [];

    public function mount()
    {
        $user = auth()->user();
        if ($user->facility_id) {
            $this->facility = Facility::find($user->facility_id);
        }
        $this->loadReferrals();
    }

public function selectReferral($id)
{
    $this->selectedReferral = Referral::where(function ($query) {
        $query->where('referring_facility_id', auth()->user()->facility_id)
              ->orWhere('receiving_facility_id', auth()->user()->facility_id);
    })->with(['referringFacility', 'receivingFacility'])->findOrFail($id);
}

public function loadReferrals()
{
    if (!$this->facility) return;

    if ($this->tab === 'incoming') {
        $this->referrals = Referral::where('receiving_facility_id', $this->facility->id)
            ->with(['referringFacility', 'receivingFacility'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->toArray();
    } else {
        $this->referrals = Referral::where('referring_facility_id', $this->facility->id)
            ->with(['referringFacility', 'receivingFacility'])
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->toArray();
    }
}

    public function acceptReferral()
    {
        if (!$this->selectedReferral) return;

        $router = app(ReferralRouter::class);
        $router->acceptReferral($this->selectedReferral);
        
        session()->flash('message', 'Referral accepted.');
        $this->loadReferrals();
        $this->selectedReferral = Referral::find($this->selectedReferral->id);
    }

    public function openRejectModal()
    {
        $this->showRejectModal = true;
    }

    public function rejectReferral()
    {
        if (!$this->selectedReferral || empty($this->rejectionReason)) return;

        $router = app(ReferralRouter::class);
        $router->rejectReferral($this->selectedReferral, $this->rejectionReason);
        
        $this->showRejectModal = false;
        $this->rejectionReason = '';
        session()->flash('message', 'Referral rejected.');
        $this->loadReferrals();
    }

    public function findFacilities()
    {
        if (!$this->facility || empty($this->referral_type)) return;

        $router = app(ReferralRouter::class);
        $this->suggestedFacilities = $router->findBestDestination(
            $this->facility,
            $this->referral_type,
            $this->urgency
        );
    }

    public function createReferral()
    {
        $this->validate([
            'referral_type' => 'required',
            'reason_for_referral' => 'required|min:10',
            'receiving_facility_id' => 'required|exists:facilities,id',
        ]);

        $router = app(ReferralRouter::class);
        $router->createReferral([
            'referring_facility_id' => $this->facility->id,
            'receiving_facility_id' => $this->receiving_facility_id,
            'referral_type' => $this->referral_type,
            'urgency' => $this->urgency,
            'reason_for_referral' => $this->reason_for_referral,
            'clinical_summary' => $this->clinical_summary,
            'patient_age_group' => $this->patient_age_group,
            'patient_gender' => $this->patient_gender,
            'patient_is_stable' => $this->patient_is_stable,
            'requires_ambulance' => $this->requires_ambulance,
        ]);

        $this->reset(['showCreateForm', 'referral_type', 'urgency', 'reason_for_referral',
            'clinical_summary', 'patient_age_group', 'patient_gender', 'receiving_facility_id',
            'suggestedFacilities']);
        
        session()->flash('message', 'Referral created.');
        $this->loadReferrals();
        $this->tab = 'outgoing';
    }

    public function render()
    {
        return view('livewire.referral.referral-manager', [
            'referralTypes' => Referral::referralTypes(),
        ])->layout('layouts.app');
    }
}