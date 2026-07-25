<?php
// app/Livewire/Admin/PartnerManager.php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\Partners\PartnerApiService;

class PartnerManager extends Component
{
    public array $partners = [];
    public string $testCrisisType = 'suicide_self_harm';
    public string $testLanguage = 'sw';
    public ?array $testResult = null;

    public function mount()
    {
        $this->partners = PartnerApiService::allPartners();
    }

    public function testFindPartner()
    {
        $service = new PartnerApiService();
        $partner = $service->findPartner($this->testCrisisType, $this->testLanguage);

        $this->testResult = [
            'crisis_type' => $this->testCrisisType,
            'language' => $this->testLanguage,
            'partner_found' => $partner !== null,
            'partner' => $partner,
        ];
    }

    public function render()
    {
        return view('livewire.admin.partner-manager')->layout('layouts.app');
    }
}