<?php
// app/Notifications/CrisisSupportNotification.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CrisisSupportNotification extends Notification
{
    protected string $language;

    public function __construct(string $language = 'en')
    {
        $this->language = $language;
    }

    public function via($notifiable): array
    {
        return ['sms'];
    }

    public function toSms($notifiable): string
    {
        if ($this->language === 'sw') {
            return "🤝 Una maana. Maisha yako yana maana.\n\n" .
                   "Piga simu: 1190\n" .
                   "Kuna mtu yuko tayari kusikiliza.\n" .
                   "Hauko peke yako.\n\n" .
                   "— Nafasi";
        }

        return "🤝 You matter. Your life matters.\n\n" .
               "Call: 1190\n" .
               "Someone is ready to listen.\n" .
               "You are not alone.\n\n" .
               "— Nafasi";
    }
}