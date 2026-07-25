<?php
// app/Notifications/AnonymousReportConfirmationNotification.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AnonymousReportConfirmationNotification extends Notification
{
    protected array $report;

    public function __construct(array $report)
    {
        $this->report = $report;
    }

    public function via($notifiable): array
    {
        return ['sms'];
    }

    public function toSms($notifiable): string
    {
        $msg = "🛡️ Nafasi: Report Submitted\n\n";
        $msg .= "Reference: #{$this->report['uuid']}\n";
        $msg .= "Type: {$this->report['type']}\n";
        $msg .= "Status: Submitted to authorities\n\n";
        $msg .= "You are anonymous. Save your reference number.\n";
        $msg .= "Thank you for your courage.";
        
        return $msg;
    }
}