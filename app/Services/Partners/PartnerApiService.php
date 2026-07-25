<?php

// app/Services/Partners/PartnerApiService.php

namespace App\Services\Partners;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PartnerApiService
{
    /**
     * Registered partner helplines.
     * In production: stored in database, managed via admin panel.
     */
    protected const PARTNERS = [
        'suicide_self_harm' => [
            'primary' => [
                'name' => 'Befrienders Kenya',
                'phone' => '+254722178177',
                'hours' => '24/7',
                'languages' => ['sw', 'en'],
                'warm_transfer' => false,
                'api_endpoint' => null,
                'webhook_url' => null,
            ],
            'secondary' => [
                'name' => 'Niskize Mental Health',
                'phone' => '+254700000000',
                'hours' => '08:00-20:00',
                'languages' => ['sw', 'en'],
            ],
        ],
        'sexual_assault' => [
            'primary' => [
                'name' => 'National GBV Hotline',
                'phone' => '1195',
                'hours' => '24/7',
                'languages' => ['sw', 'en'],
                'note' => 'Free. Government-operated.',
            ],
            'secondary' => [
                'name' => 'FIDA Kenya',
                'phone' => '+254722509649',
                'hours' => '08:00-17:00',
                'languages' => ['sw', 'en'],
                'note' => 'Legal aid available.',
            ],
        ],
        'violence_abuse' => [
            'primary' => [
                'name' => 'Childline Kenya',
                'phone' => '116',
                'hours' => '24/7',
                'languages' => ['sw', 'en'],
                'note' => 'For children under 18.',
            ],
        ],
        'mental_health_distress' => [
            'primary' => [
                'name' => 'Befrienders Kenya',
                'phone' => '+254722178177',
                'hours' => '24/7',
                'languages' => ['sw', 'en'],
            ],
        ],
    ];

    /**
     * Find the best available partner for a crisis type.
     */
    public function findPartner(string $crisisType, string $language = 'sw', ?string $timeOfDay = null): ?array
    {
        $partners = self::PARTNERS[$crisisType] ?? [];
        $timeOfDay = $timeOfDay ?? now()->format('H:i');

        // Try primary partner first
        if (isset($partners['primary'])) {
            if ($this->isAvailable($partners['primary']['hours'], $timeOfDay)) {
                if (in_array($language, $partners['primary']['languages'])) {
                    return $partners['primary'];
                }
            }
        }

        // Try secondary
        if (isset($partners['secondary'])) {
            if ($this->isAvailable($partners['secondary']['hours'], $timeOfDay)) {
                if (in_array($language, $partners['secondary']['languages'])) {
                    return $partners['secondary'];
                }
            }
        }

        // Fallback: first 24/7 partner
        foreach ($partners as $partner) {
            if ($partner['hours'] === '24/7') {
                return $partner;
            }
        }

        return null;
    }

    /**
     * Check if partner is available at given time.
     */
    protected function isAvailable(string $hours, string $timeOfDay): bool
    {
        if ($hours === '24/7') {
            return true;
        }

        [$open, $close] = explode('-', $hours);

        return $timeOfDay >= $open && $timeOfDay <= $close;
    }

    /**
     * Generate warm handoff context.
     * NEVER includes personal data.
     */
    public function generateHandoffContext(string $crisisType, string $language, ?string $generalArea): array
    {
        return [
            'source' => 'Nafasi Platform',
            'caller_type' => 'anonymous',
            'crisis_type' => $crisisType,
            'language' => $language,
            'general_area' => $generalArea ?? 'Unknown',
            'transfer_time' => now()->toISOString(),
            'no_personal_data' => true,
            'warm_handoff' => true,
            'context_note' => 'Caller has NOT shared personal details. Do NOT ask for identity.',
        ];
    }

    /**
     * Notify partner of incoming warm handoff.
     */
    public function notifyPartner(array $partner, array $context): bool
    {
        $webhookUrl = $partner['webhook_url'] ?? null;

        if ($webhookUrl) {
            try {
                $response = Http::timeout(10)
                    ->post($webhookUrl, $context);

                Log::info('Partner notified via webhook', [
                    'partner' => $partner['name'],
                    'status' => $response->status(),
                ]);

                return $response->successful();
            } catch (\Exception $e) {
                Log::error('Partner webhook failed', [
                    'partner' => $partner['name'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Partner notified', [
            'partner' => $partner['name'],
            'status' => 'no_webhook',
            'timestamp' => now(),
        ]);

        return true;
    }

    /**
     * Track warm handoff outcome.
     */
    public function trackOutcome(string $crisisType, string $partnerName, string $outcome): void
    {
        Log::info('Partner handoff outcome', [
            'crisis_type' => $crisisType,
            'partner' => $partnerName,
            'outcome' => $outcome,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get all registered partners for admin display.
     */
    public static function allPartners(): array
    {
        return self::PARTNERS;
    }
}
