<?php
// app/Http/Controllers/Api/PartnerHandoffController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Partners\PartnerApiService;
use Illuminate\Http\Request;

class PartnerHandoffController extends Controller
{
    protected PartnerApiService $partnerService;

    public function __construct()
    {
        $this->partnerService = new PartnerApiService();
    }

    /**
     * Find available partners for a crisis type.
     */
    public function findPartner(Request $request)
    {
        $request->validate([
            'crisis_type' => 'required|string',
            'language' => 'nullable|string|max:10',
            'general_area' => 'nullable|string|max:100',
        ]);

        $partner = $this->partnerService->findPartner(
            $request->crisis_type,
            $request->language ?? 'sw',
            now()->format('H:i')
        );

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'No partner available at this time.',
                'fallback' => 'Use internal coordinator or call 1190.',
            ]);
        }

        $context = $this->partnerService->generateHandoffContext(
            $request->crisis_type,
            $request->language ?? 'sw',
            $request->general_area ?? null
        );

        // Notify partner
        $this->partnerService->notifyPartner($partner, $context);

        return response()->json([
            'success' => true,
            'partner' => [
                'name' => $partner['name'],
                'phone' => $partner['phone'],
                'note' => $partner['note'] ?? null,
            ],
            'handoff_context' => $context,
            'message' => "Warm handoff to {$partner['name']} initiated.",
        ]);
    }

    /**
     * Track handoff outcome.
     */
    public function trackOutcome(Request $request)
    {
        $request->validate([
            'crisis_type' => 'required|string',
            'partner_name' => 'required|string',
            'outcome' => 'required|string|in:connected,resolved,transferred,caller_hung_up,no_answer',
        ]);

        $this->partnerService->trackOutcome(
            $request->crisis_type,
            $request->partner_name,
            $request->outcome
        );

        return response()->json(['success' => true]);
    }

    /**
     * List all registered partners.
     */
    public function listPartners()
    {
        return response()->json([
            'success' => true,
            'partners' => PartnerApiService::allPartners(),
        ]);
    }
}