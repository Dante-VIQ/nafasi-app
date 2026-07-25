<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $body = $request->input('Body', []);
        $stkCallback = $body['stkCallback'] ?? [];
        $resultCode  = $stkCallback['ResultCode'] ?? 1;

        if ($resultCode !== 0) {
            Log::warning('M‑Pesa payment failed', $stkCallback);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Failed']);
        }

        $items = $stkCallback['CallbackMetadata']['Item'] ?? [];
        $amount   = collect($items)->firstWhere('Name', 'Amount')['Value'] ?? 0;
        $mpesaRef = collect($items)->firstWhere('Name', 'MpesaReceiptNumber')['Value'] ?? null;
        $phone    = collect($items)->firstWhere('Name', 'PhoneNumber')['Value'] ?? null;

        // Find the facility by the phone used to pay
        $facility = Facility::where('payment_phone', $phone)
            ->orWhere('phone', $phone)
            ->first();

        if ($facility) {
            $this->activateSubscription($facility, $amount, $mpesaRef);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    protected function activateSubscription(Facility $facility, float $amount, ?string $reference): void
    {
        $tier = $facility->subscription_tier;
        
        $facility->update([
            'subscription_status'   => 'active',
            'subscription_expires_at' => now()->addMonths(1),
            'last_payment_reference'  => $reference,
            'last_payment_at'         => now(),
        ]);

        Log::info("Subscription activated for {$facility->name} – {$tier} – KSh {$amount}");
    }
}