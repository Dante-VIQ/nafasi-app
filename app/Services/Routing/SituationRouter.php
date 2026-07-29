<?php

namespace App\Services\Routing;

use App\Models\Tenant;
use App\Models\Tenant\Facility;
use App\Services\ML\MlServiceClient;
use Illuminate\Support\Facades\Log;

class SituationRouter
{
    public function __construct(
        protected MlServiceClient $ml
    ) {}

    /**
     * Guarantee a tenant is loaded before we touch the tenant database.
     * On tenant domains it's already set; on central domains (127.0.0.1)
     * we fall back to the first active tenant so the landing page can
     * search facilities during development/demo.
     */
    protected function ensureTenant(): void
    {
        if (tenancy()->initialized) {
            return;
        }

        // 1. Try to identify by domain (same as Stancl's middleware)
        $host = request()->getHost();
        $tenant = Tenant::whereHas('domains', fn($q) => $q->where('domain', $host))->first();

        if ($tenant) {
            tenancy()->initialize($tenant);
            return;
        }

        // 2. Central domain or unknown host → use the first active tenant
        $tenant = Tenant::where('status', 'active')->first();
        if ($tenant) {
            tenancy()->initialize($tenant);
        }
    }

    public function route(string $text, ?float $lat = null, ?float $lng = null): array
    {
        // Tenant must be ready before any facility query
        $this->ensureTenant();

        $classification = $this->classifySafely($text);

        // 1. Crisis takes priority
        if ($classification['is_crisis'] ?? false) {
            return $this->crisisResponse($classification['language'] ?? 'en');
        }

        // 2. Anonymous report
        if (($classification['is_anonymous_report'] ?? false) || $this->mentionsAnonymousReport($text)) {
            return [
                'type' => 'anonymous_report',
                'message' => 'You can submit an anonymous report safely.',
                'redirect_url' => route('report.anonymous'),
            ];
        }

        // 3. Emergency
        if ($classification['is_emergency'] ?? false) {
            return $this->emergencyResponse(
                $classification['emergency_type'] ?? 'general',
                $classification['language'] ?? 'en'
            );
        }

        // 4. Dispatch needed
        if ($classification['needs_dispatch'] ?? false) {
            return $this->dispatchResponse($classification['language'] ?? 'en');
        }

        // 5. Find facilities by hints
        $facilities = $this->findFacilitiesByHints(
            $classification['facility_hints'] ?? ['hospital'],
            $lat,
            $lng
        );

        return [
            'type' => 'facilities',
            'detected_language' => $classification['language'] ?? 'en',
            'facility_hints' => $classification['facility_hints'] ?? [],
            'confidence' => $classification['confidence'] ?? 0.5,
            'facilities' => $facilities,
        ];
    }

    /**
     * Call the ML classifier, but never let its failure crash routing.
     */
    protected function classifySafely(string $text): array
    {
        try {
            return $this->ml->classify($text);
        } catch (\Throwable $e) {
            Log::error('SituationRouter: ML classification failed, falling back. ' . $e->getMessage());

            return [
                'is_crisis' => false,
                'is_anonymous_report' => false,
                'is_emergency' => false,
                'needs_dispatch' => false,
                'language' => 'en',
                'facility_hints' => ['hospital'],
                'confidence' => 0.0,
            ];
        }
    }

    /**
     * Keyword fallback for anonymous-report intent.
     */
    protected function mentionsAnonymousReport(string $text): bool
    {
        $text = strtolower($text);
        return str_contains($text, 'report anonymously')
            || str_contains($text, 'anonymous report');
    }

protected function findFacilitiesByHints(array $hints, ?float $lat, ?float $lng): array
{
    $query = Facility::query()
        ->where('is_active', true)
        ->where('registration_status', 'approved');

    if (!empty($hints)) {
        $query->whereIn('facility_type', $hints);
    }

    if ($lat !== null && $lng !== null) {
        // Haversine formula with proper handling for equatorial and meridian edge cases
        $haversine = '(6371 * acos(LEAST(1, GREATEST(-1, 
            cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) 
            + sin(radians(?)) * sin(radians(latitude))
        ))))';

        $query->selectRaw("*, {$haversine} AS distance", [$lat, $lng, $lat])
            ->orderBy('distance')
            ->having('distance', '<', 20);
    }

    // Order by congestion level (low → moderate → everything else)
    $query->orderByRaw("
        CASE 
            WHEN congestion_status = 'low' THEN 1 
            WHEN congestion_status = 'moderate' THEN 2 
            ELSE 3 
        END
    ");

    return $query->take(10)->get()->toArray();
}

    protected function crisisResponse(string $language): array
    {
        $helplines = ['sw' => '1190', 'en' => '1190', 'sheng' => '1190'];

        return [
            'type' => 'crisis',
            'message' => $language === 'sw'
                ? 'Una maana. Maisha yako yana maana. Tafadhali kaa. Kuna mtu yuko tayari kusikiliza.'
                : 'You matter. Your life matters. Please stay. Someone is ready to listen.',
            'emergency_number' => $helplines[$language] ?? '1190',
            'facilities' => [],
        ];
    }

    protected function emergencyResponse(string $emergencyType, string $language): array
    {
        $messages = [
            'fire' => [
                'sw' => 'Moto umeripotiwa. Piga 999 na uhame mara moja.',
                'en' => 'Fire reported. Call 999 and evacuate immediately.',
            ],
            'accident' => [
                'sw' => 'Ajali imetokea. Msaada wa kimatibabu unahitajika.',
                'en' => 'Accident with injuries. Medical help needed.',
            ],
            'police' => [
                'sw' => 'Tukio la usalama. Polisi wamearifiwa.',
                'en' => 'Security incident. Police have been notified.',
            ],
        ];

        $type = isset($messages[$emergencyType]) ? $emergencyType : 'general';

        return [
            'type' => 'emergency',
            'message' => $messages[$type][$language] ?? (
                $language === 'sw'
                    ? 'Hii inaonekana ni dharura. Piga 999 mara moja.'
                    : 'This sounds like an emergency. Call 999 immediately.'
            ),
            'emergency_number' => '999',
            'facilities' => [],
        ];
    }

    protected function dispatchResponse(string $language): array
    {
        return [
            'type' => 'dispatch',
            'message' => $language === 'sw'
                ? 'Inaonekana unahitaji msaada kukujia. Mratibu atawasiliana nawe.'
                : 'It sounds like you need help to come to you. A coordinator will reach out.',
            'emergency_number' => null,
            'facilities' => [],
        ];
    }
}