<?php
// app/Services/Payments/MpesaService.php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    protected $consumerKey;
    protected $consumerSecret;
    protected $passkey;
    protected $shortcode;

    public function __construct()
    {
        $this->consumerKey    = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->passkey        = config('services.mpesa.passkey');
        $this->shortcode      = config('services.mpesa.shortcode');
    }

    /**
     * Initiate an STK Push (M‑Pesa Express).
     * Returns a checkout request ID for tracking.
     */
    public function stkPush(string $phone, float $amount, string $accountReference, string $description): array
    {
        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $response = Http::withToken($this->authenticate())
            ->post('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest', [
                'BusinessShortCode' => $this->shortcode,
                'Password'          => $password,
                'Timestamp'         => $timestamp,
                'TransactionType'   => 'CustomerBuyGoodsOnline',
                'Amount'            => $amount,
                'PartyA'            => $this->formatPhone($phone),
                'PartyB'            => $this->shortcode,
                'PhoneNumber'       => $this->formatPhone($phone),
                'CallBackURL'       => route('mpesa.callback'),
                'AccountReference'  => $accountReference,
                'TransactionDesc'   => $description,
            ]);

        return $response->json();
    }

    protected function authenticate(): string
    {
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');

        return $response->json()['access_token'] ?? '';
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 9) {
            return '254' . $phone;
        }
        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }
        return $phone;
    }
}