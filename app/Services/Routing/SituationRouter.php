<?php

namespace App\Services\Routing;

use App\Models\Tenant\Facility;
use App\Services\ML\MlServiceClient;

class SituationRouter
{
    protected MlServiceClient $ml;

    public function __construct()
    {
        $this->ml = new MlServiceClient;
    }

    public function route(string $text, ?float $lat = null, ?float $lng = null): array
    {
        $classification = $this->ml->classify($text);

        // 1. Crisis takes priority
        if ($classification['is_crisis']) {
            return $this->crisisResponse($classification['language'] ?? 'en');
        }

        // 2. Anonymous report
        if ($classification['is_anonymous_report']) {
            return [
                'type' => 'anonymous_report',
                'message' => 'You can submit an anonymous report safely.',
                'redirect_url' => route('report.anonymous'),
            ];
        }

        // 3. Emergency
        if ($classification['is_emergency']) {
            return $this->emergencyResponse(
                $classification['emergency_type'] ?? 'general',
                $classification['language'] ?? 'en'
            );
        }

        // 4. Dispatch needed
        if ($classification['needs_dispatch']) {
            return $this->dispatchResponse($classification['language'] ?? 'en');
        }

        if (str_contains(strtolower($text), 'report anonymously') ||
    str_contains(strtolower($text), 'anonymous report')) {
            return [
                'type' => 'anonymous_report',
                'message' => 'You can submit an anonymous report safely.',
                'redirect_url' => route('report.anonymous'),
            ];
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

    protected function findFacilitiesByHints(array $hints, ?float $lat, ?float $lng): array
    {
        $query = Facility::query()
            ->where('is_active', true)
            ->where('registration_status', 'approved');

        if (! empty($hints)) {
            $query->whereIn('facility_type', $hints);
        }

        if ($lat && $lng) {
            $query->selectRaw('
                *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ', [$lat, $lng, $lat])
                ->orderBy('distance')
                ->having('distance', '<', 20);
        }

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
