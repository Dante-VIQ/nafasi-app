<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class FacilityDirectionsNotification extends Notification
{
    protected array $facility;

    public function __construct(array $facility)
    {
        $this->facility = $facility;
    }

    public function via($notifiable): array
    {
        return ['sms'];
    }

    public function toSms($notifiable): string
    {
        $msg = "Nafasi: {$this->facility['name']}\n";
        $msg .= "📞 {$this->facility['phone']}\n";
        $msg .= "📍 {$this->facility['address']}\n";
        
        if (!empty($this->facility['distance'])) {
            $msg .= "📏 " . round($this->facility['distance'], 1) . " km away\n";
        }
        
        if (!empty($this->facility['congestion_status'])) {
            $msg .= "⏳ Wait: " . ucfirst($this->facility['congestion_status']) . "\n";
        }
        
        $msg .= "Call 999 for emergencies.";
        
        return $msg;
    }
}