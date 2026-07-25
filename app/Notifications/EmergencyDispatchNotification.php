<?php
// app/Notifications/EmergencyDispatchNotification.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class EmergencyDispatchNotification extends Notification
{
    protected array $dispatch;

    public function __construct(array $dispatch)
    {
        $this->dispatch = $dispatch;
    }

    public function via($notifiable): array
    {
        return ['sms'];
    }

    public function toSms($notifiable): string
    {
        $msg = "🚨 Nafasi: Help is on the way!\n\n";
        $msg .= "Responder: {$this->dispatch['responder']['name']}\n";
        $msg .= "Qualification: {$this->dispatch['responder']['qualification']}\n";
        $msg .= "ETA: {$this->dispatch['eta_to_patient_minutes']} minutes\n\n";
        
        if (!empty($this->dispatch['instructions'])) {
            $msg .= "While you wait:\n";
            foreach (array_slice($this->dispatch['instructions'], 0, 3) as $instruction) {
                $msg .= "• {$instruction}\n";
            }
        }
        
        return $msg;
    }
}