<?php
// app/Http/Controllers/SmsWebhookController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant\Appointment;

class SmsWebhookController extends Controller
{
    /**
     * Handle incoming SMS replies from users.
     */
    public function handle(Request $request)
    {
    $validated = $request->validate([
        'from' => 'required|string|max:20',
        'text' => 'required|string|max:500',
        'id'   => 'nullable|string|max:100',
    ]);

    $from = $validated['from'];
    $text = trim($validated['text']);

        // Handle booking confirmations
        if ($text === '1') {
            $this->confirmBooking($from);
        } elseif ($text === '2') {
            $this->cancelBooking($from);
        }

        return response('OK', 200);
    }

    protected function confirmBooking(string $phone): void
    {
        $appointment = Appointment::where('patient_phone', $phone)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($appointment) {
            $appointment->update(['status' => 'confirmed']);
            
            app(SmsService::class)->send($phone, 
                "✅ Your appointment at {$appointment->facility->name} is confirmed.\n" .
                "See you on {$appointment->scheduled_at->format('d M Y, H:i')}"
            );
        }
    }

    protected function cancelBooking(string $phone): void
    {
        $appointment = Appointment::where('patient_phone', $phone)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($appointment) {
            $appointment->update(['status' => 'cancelled']);
            
            app(SmsService::class)->send($phone, 
                "Your appointment has been cancelled.\n" .
                "Book again at nafasi.health"
            );
        }
    }
}