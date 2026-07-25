<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class BookingConfirmationNotification extends Notification
{
    protected array $booking;

    public function __construct(array $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable): array
    {
        return ['sms'];
    }

    public function toSms($notifiable): string
    {
        $msg = "✅ Nafasi: Booking Confirmed!\n\n";
        $msg .= "Facility: {$this->booking['facility_name']}\n";
        $msg .= "Date: {$this->booking['date']}\n";
        $msg .= "Time: {$this->booking['time']}\n";
        $msg .= "Ref: {$this->booking['reference']}\n\n";
        $msg .= "Reply 1 to confirm, 2 to cancel.";
        
        return $msg;
    }
}