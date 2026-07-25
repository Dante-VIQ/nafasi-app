<?php
// app/Livewire/Facility/PaymentPage.php

namespace App\Livewire\Facility;

use Livewire\Component;
use App\Models\Tenant\Facility;
use App\Services\Payments\MpesaService;
use Illuminate\Support\Facades\Auth;

class PaymentPage extends Component
{
    public Facility $facility;
    public string $paymentPhone = '';
    public bool $paymentInitiated = false;
    public string $paymentMessage = '';

    public function mount()
    {
        $this->facility = Facility::findOrFail(Auth::user()->facility_id);
        $this->paymentPhone = $this->facility->payment_phone ?? $this->facility->phone;
    }

    public function initiatePayment()
    {
        $tier = $this->facility->subscription_tier;
        $prices = [
            'chemist' => 500,
            'clinic' => 1500,
            'hospital' => 5000,
            'government' => 1000,
        ];
        $amount = $prices[$tier] ?? 1000;

        $mpesa = app(MpesaService::class);
        $result = $mpesa->stkPush(
            $this->paymentPhone,
            $amount,
            $this->facility->slug,
            "Nafasi {$tier} subscription"
        );

        $this->paymentInitiated = true;
        $this->paymentMessage = $result['ResponseCode'] === '0'
            ? 'STK Push sent. Check your phone to complete payment.'
            : 'Payment initiation failed. Please try again.';
    }

    public function render()
    {
        return view('livewire.facility.payment-page', [
            'subscription' => $this->facility->subscription_status,
            'tier' => $this->facility->subscription_tier,
            'expiresAt' => $this->facility->subscription_expires_at,
        ])->layout('layouts.app');
    }
}