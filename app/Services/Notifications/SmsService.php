<?php
// app/Services/Notifications/SmsService.php

namespace App\Services\Notifications;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?AfricasTalking $gateway = null;
    protected string $senderId;

    public function __construct()
    {
        $username = config('services.africastalking.username');
        $apiKey = config('services.africastalking.api_key');
        $this->senderId = config('services.africastalking.sender_id', 'Nafasi');

        if ($username && $apiKey && $username !== 'sandbox') {
            $this->gateway = new AfricasTalking($username, $apiKey);
        }
    }

    /**
     * Send a single SMS.
     */
    public function send(string $phone, string $message): bool
    {
        if (!$this->gateway) {
            Log::info('SMS (sandbox)', ['phone' => $phone, 'message' => $message]);
            return true; // Sandbox mode — log only
        }

        try {
            $sms = $this->gateway->sms();
            $result = $sms->send([
                'to' => $phone,
                'message' => $message,
                'from' => $this->senderId,
            ]);

            Log::info('SMS sent', ['phone' => $phone, 'result' => $result]);
            return true;
        } catch (\Exception $e) {
            Log::error('SMS failed', ['phone' => $phone, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send booking confirmation to patient.
     */
    public function sendBookingConfirmation(string $phone, string $facilityName, string $dateTime, string $reference): void
    {
        $message = "Nafasi: Appointment at {$facilityName} on {$dateTime}. Ref: {$reference}. Reply HELP for assistance.";
        $this->send($phone, $message);
    }

    /**
     * Send dispatch alert to responder.
     */
    public function sendResponderAlert(string $phone, string $emergencyType, string $location, string $dispatchRef): void
    {
        $message = "NAFASI EMERGENCY: {$emergencyType} at {$location}. Ref: {$dispatchRef}. Respond immediately. Reply YES to confirm.";
        $this->send($phone, $message);
    }

    /**
     * Send dispatch alert to rider.
     */
    public function sendRiderAlert(string $phone, string $pickupLocation, string $destination, string $dispatchRef): void
    {
        $message = "NAFASI RIDE: Pickup responder at {$pickupLocation}, then to patient. Ref: {$dispatchRef}. Reply YES to accept.";
        $this->send($phone, $message);
    }

    /**
     * Notify facility of incoming emergency.
     */
    public function notifyFacilityEmergency(string $phone, string $emergencyType, string $eta, string $dispatchRef): void
    {
        $message = "NAFASI: Incoming {$emergencyType}. ETA: {$eta}. Ref: {$dispatchRef}. Prepare to receive.";
        $this->send($phone, $message);
    }

    /**
     * Send anonymous report confirmation.
     */
    public function sendReportConfirmation(string $phone, string $reportRef): void
    {
        $message = "Nafasi: Anonymous report received. Ref: {$reportRef}. Thank you for your courage.";
        $this->send($phone, $message);
    }
}