<?php
// app/Http/Controllers/UssdController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Routing\SituationRouter;
use App\Services\ML\MlServiceClient;

class UssdController extends Controller
{
    protected SituationRouter $router;
    protected MlServiceClient $ml;

    public function __construct()
    {
        $this->router = new SituationRouter();
        $this->ml = new MlServiceClient();
    }

    /**
     * Handle USSD request from Africa's Talking.
     * This is the single entry point for all USSD interactions.
     */
    public function handle(Request $request)
    {
    $validated = $request->validate([
        'sessionId'   => 'required|string|max:255',
        'phoneNumber' => 'required|string|max:20',
        'networkCode' => 'nullable|string|max:10',
        'serviceCode' => 'required|string|max:20',
        'text'        => 'nullable|string|max:500',
    ]);

    $sessionId   = $validated['sessionId'];
    $phoneNumber = $validated['phoneNumber'];
    $networkCode = $validated['networkCode'] ?? null;
    $serviceCode = $validated['serviceCode'];
    $text        = $validated['text'] ?? '';

    // parse inputs (same as before)
    $inputs      = explode('*', $text);
    $currentStep = count($inputs);
    $lastInput   = end($inputs);

        // If empty (first request), show welcome
        if (empty($text)) {
            return $this->respond($this->getWelcomeMenu(), 'CON');
        }

        // Route based on the first input (menu selection)
        $menuChoice = $inputs[0];

        return match ($menuChoice) {
            '1' => $this->handleFindHelp($inputs, $sessionId, $phoneNumber),
            '2' => $this->handleEmergency($inputs),
            '3' => $this->handleCrisis($inputs),
            default => $this->respond("Invalid choice. Please try again.\n\n" . $this->getWelcomeMenu(), 'CON'),
        };
    }

    /**
     * Welcome menu — first screen user sees.
     */
    protected function getWelcomeMenu(): string
    {
        return "Welcome to Nafasi\n" .
               "1. Find Help Nearby\n" .
               "2. Emergency\n" .
               "3. Crisis Support\n";
    }

    /**
     * Path 1: Find Help Nearby.
     * User describes what they need, we route them.
     */
    protected function handleFindHelp(array $inputs, string $sessionId, string $phoneNumber): string
    {
        // Step 1: Ask what they need
        if (count($inputs) === 1) {
            return $this->respond(
                "What do you need?\n" .
                "Type: pharmacy, lab, dental, maternity, hospital, or describe your need.",
                'CON'
            );
        }

        // Step 2: User has typed their need
        $userNeed = $inputs[1];

        // Classify using ML service (with fallback)
        $classification = $this->ml->classify($userNeed);

        // Route
        $result = $this->router->route($userNeed);

        if ($result['type'] === 'emergency') {
            return $this->respond(
                "EMERGENCY: {$result['message']}\n" .
                "Call {$result['emergency_number']} immediately.\n" .
                "Reply 0 to go back.",
                'END'
            );
        }

        if ($result['type'] === 'crisis') {
            return $this->respond(
                "CRISIS SUPPORT\n" .
                "{$result['message']}\n" .
                "Call {$result['emergency_number']}\n" .
                "You are not alone.",
                'END'
            );
        }

        // Show facilities
        return $this->formatFacilities($result);
    }

    /**
     * Path 2: Emergency.
     */
    protected function handleEmergency(array $inputs): string
    {
        return $this->respond(
            "EMERGENCY\n" .
            "Call 999 immediately.\n" .
            "For fire: ask for Fire Department\n" .
            "For accident: ask for Ambulance\n" .
            "For police: ask for Police\n" .
            "Reply 0 to go back.",
            'END'
        );
    }

    /**
     * Path 3: Crisis Support.
     */
    protected function handleCrisis(array $inputs): string
    {
        return $this->respond(
            "You are not alone.\n" .
            "Call 1190 to speak with a counselor.\n" .
            "Free. Confidential. 24/7.\n" .
            "Reply 0 to go back.",
            'END'
        );
    }

    /**
     * Format facilities for USSD display (max 182 chars per screen).
     */
    protected function formatFacilities(array $result): string
    {
        if (empty($result['facilities'])) {
            return $this->respond(
                "No facilities found nearby.\nTry expanding your search.\nReply 0 to go back.",
                'END'
            );
        }

        $output = "Found " . count($result['facilities']) . " facilities:\n";
        $count = 1;

        foreach ($result['facilities'] as $facility) {
            $name = substr($facility['name'], 0, 25);
            $distance = isset($facility['distance']) ? round($facility['distance'], 1) . 'km' : '';
            $congestion = $facility['congestion_status'] ?? '';
            $congestionIcon = match ($congestion) {
                'low' => '🟢',
                'moderate' => '🟡',
                'high' => '🔴',
                default => '⚪',
            };

            $output .= "{$count}. {$name} {$distance} {$congestionIcon}\n";
            $count++;

            // Limit to 5 facilities for USSD
            if ($count > 5) break;
        }

          // Offer to send results via SMS
    $output .= "\nReply 9 to receive these results via SMS.";


        return $this->respond($output, 'CON');
    }

    /**
     * Format response for Africa's Talking.
     * CON = continue session, END = end session.
     */
    protected function respond(string $message, string $type = 'CON'): string
    {
        return "{$type} {$message}";
    }
}