<?php
// app/Services/SmsService.php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?AfricasTalking $at = null;

    public function __construct()
    {
        $username = config('services.africastalking.username');
        $apiKey   = config('services.africastalking.api_key');

        if ($username && $apiKey && $username !== 'sandbox') {
            $this->at = new AfricasTalking($username, $apiKey);
        }
    }

    /**
     * Send SMS notification.
     */
    public function send(string $phone, string $message): bool
    {
        // If no Africa's Talking instance (sandbox or missing creds), log and return
        if (!$this->at) {
            Log::info('SMS (sandbox): ' . $phone . ' - ' . $message);
            return true;
        }

        try {
            $sms = $this->at->sms();
            $result = $sms->send([
                'to'      => $phone,
                'message' => $message,
                'from'    => 'Nafasi',
            ]);

            return ($result['status'] ?? '') === 'success';
        } catch (\Exception $e) {
            Log::error('SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send facility directions via SMS.
     */
    public function sendFacilityDirections(string $phone, array $facility): bool
    {
        $message = "Nafasi: {$facility['name']}\n" .
                   "📞 {$facility['phone']}\n" .
                   "📍 {$facility['address']}\n";

        if (!empty($facility['distance'])) {
            $message .= "📏 " . round($facility['distance'], 1) . " km away\n";
        }

        $message .= "Call 999 for emergencies.";

        return $this->send($phone, $message);
    }

    /**
     * Send emergency dispatch confirmation.
     */
    public function sendDispatchConfirmation(string $phone, array $dispatch): bool
    {
        $message = "Nafasi: Help is on the way!\n" .
                   "Responder: {$dispatch['responder']['name']}\n" .
                   "ETA: {$dispatch['eta_to_patient_minutes']} minutes\n" .
                   "Stay calm. Help is coming.";

        return $this->send($phone, $message);
    }

    /**
     * Send booking confirmation.
     */
    public function sendBookingConfirmation(string $phone, array $booking): bool
    {
        $message = "Nafasi: Booking confirmed!\n" .
                   "Facility: {$booking['facility_name']}\n" .
                   "Date: {$booking['date']}\n" .
                   "Reference: {$booking['reference']}";

        return $this->send($phone, $message);
    }
}