<?php

namespace App\Services\Routing;

use App\Models\Tenant\Facility;
use App\Services\Decision\DecisionEngine;
use App\Services\Escalation\EscalationEngine;
use App\Services\ML\MlServiceClient;
use App\Services\NLU\EntityExtractor;
use App\Services\Risk\RiskAssessor;
use App\Services\Verification\LlmVerifier;
use Illuminate\Support\Facades\Log;

class SituationRouter
{
    protected EntityExtractor $extractor;

    protected EscalationEngine $escalation;

    protected RiskAssessor $risk;

    protected DecisionEngine $decision;

    protected LlmVerifier $llm;

    public function __construct(MlServiceClient $ml)
    {
        $this->ml = $ml;
        $this->extractor = new EntityExtractor;
        $this->risk = new RiskAssessor;
        $this->decision = new DecisionEngine;
        $this->escalation = new EscalationEngine;
        $this->llm = new LlmVerifier;
    }

    public function route(string $text, ?float $lat = null, ?float $lng = null): array
    {
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
                $classification['language'] ?? 'en',
                $lat,
                $lng
            );
        }

        // 4. Dispatch needed
        if ($classification['needs_dispatch'] ?? false) {
            return $this->dispatchResponse($classification['language'] ?? 'en', $lat, $lng);
        }

        $classification = $this->classifySafely($text);
        // ML+LLM cooperation (as specified in the AI architecture)
        $llmResult = null;
        if (($classification['confidence'] ?? 0) < 0.95) {
            $llmResult = $this->llm->verify($text, $classification);
            // If LLM overrides, merge
            if (! $llmResult['verified']) {
                $classification['facility_hints'] = ['hospital']; // fallback
                $classification['confidence'] = $llmResult['confidence'];
            }
        }

        // Store LLM result in the return array:

        // Entity Extraction ##Classification ##Language
        $entities = $this->extractor->extract($text, $classification['language'] ?? 'en');

        // Risk Assessment
        $risk = $this->risk->assess($classification, $entities);

        // Decision Making After Risk Assessment
        $decision = $this->decision->decide($classification, $entities, $risk);

        // Escalation for high risk or low confidence situations
        $escalation = $this->escalation->evaluate($risk, $decision, $classification);

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
            'risk' => $risk,
            'facilities' => $facilities,
            'decision' => $decision,
            'escalation' => $escalation,
            'llm_verification' => $llmResult,
        ];

        Log::info('Entities extracted', [
            'session' => substr(session()->getId(), 0, 8),
            'symptoms' => $entities['symptoms'],
            'people' => $entities['people'],
            'urgency' => $entities['time_urgency'],
            'severity' => $entities['severity'],
        ]);
    }

    /**
     * Call the ML classifier, but never let its failure crash routing —
     * this sits in front of crisis detection, so an ML outage must
     * degrade gracefully (fall through to keyword checks / facility
     * search) rather than 500 the entire intake flow.
     */
    protected function classifySafely(string $text): array
    {
        try {
            return $this->ml->classify($text);
        } catch (\Throwable $e) {
            Log::error('SituationRouter: ML classification failed, falling back. '.$e->getMessage());

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
     * Keyword fallback for anonymous-report intent, independent of the
     * ML classifier — kept as a floor in case the classifier misses it
     * (or is down and we're on the fallback classification above).
     */
    protected function mentionsAnonymousReport(string $text): bool
    {
        $text = strtolower($text);

        return str_contains($text, 'report anonymously')
            || str_contains($text, 'anonymous report');
    }

    protected function findFacilitiesByHints(array $hints, ?float $lat, ?float $lng, bool $isEmergency = false): array
    {
        $query = Facility::query()
            ->where('is_active', true)
            ->where('registration_status', 'approved');

        if (! empty($hints)) {
            $query->whereIn('facility_type', $hints);
        }

        // Explicit null checks, not truthy checks — 0.0 is a valid
        // latitude (parts of Kenya sit right on the equator), and
        // `$lat && $lng` would silently skip distance calculation
        // for exactly those coordinates.
        if ($lat !== null && $lng !== null) {
            $query->selectRaw('
                *,
                (6371 * acos(
                    LEAST(1, GREATEST(-1,
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))
                        + sin(radians(?)) * sin(radians(latitude))
                    ))
                )) AS distance
            ', [$lat, $lng, $lat])
                ->orderBy('distance');

            // The 20km cutoff makes sense for casual browsing ("show me
            // nearby facilities") but not for a real emergency — the
            // nearest facility is still the right answer even if it's
            // farther than 20km, and an empty result is worse than a
            // distant-but-real one.
            if (! $isEmergency) {
                $query->having('distance', '<', 20);
            }
        }

        // Congestion is only a secondary preference for calm, non-
        // emergency routing. During an emergency, distance alone
        // decides — never trade a closer facility for a less busy one.
        if (! $isEmergency) {
            $query->orderByRaw("
                CASE 
                    WHEN congestion_status = 'low' THEN 1 
                    WHEN congestion_status = 'moderate' THEN 2 
                    ELSE 3 
                END
            ");
        }

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

    protected function emergencyResponse(string $emergencyType, string $language, ?float $lat = null, ?float $lng = null): array
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

        // Nearest facility wins regardless of congestion during an
        // emergency — see findFacilitiesByHints($isEmergency: true).
        $facilities = $this->findFacilitiesByHints(
            $this->facilityHintsForEmergencyType($emergencyType),
            $lat,
            $lng,
            isEmergency: true
        );

        return [
            'type' => 'emergency',
            'message' => $messages[$type][$language] ?? (
                $language === 'sw'
                    ? 'Hii inaonekana ni dharura. Piga 999 mara moja.'
                    : 'This sounds like an emergency. Call 999 immediately.'
            ),
            'emergency_number' => '999',
            'facilities' => $facilities,
        ];
    }

    /**
     * Maps an emergency type to facility_type values to search for.
     *
     * ASSUMPTION TO VERIFY: this only uses 'hospital' and 'police_station',
     * the only facility_type values seen in the codebase so far. If your
     * Facility model supports more types (e.g. a dedicated fire-station
     * type), extend this mapping accordingly — right now 'fire' falls
     * back to 'hospital' since fire-related emergencies still need
     * medical care and there's no evidenced fire-station type to route to.
     */
    protected function facilityHintsForEmergencyType(string $emergencyType): array
    {
        return match ($emergencyType) {
            'police' => ['police_station'],
            default => ['hospital'],
        };
    }

    protected function dispatchResponse(string $language, ?float $lat = null, ?float $lng = null): array
    {
        // "Needs dispatch" means help should come to them — still worth
        // surfacing the nearest facility so a coordinator/the person has
        // a concrete option, ordered purely by distance for the same
        // reason as emergencyResponse.
        $facilities = $this->findFacilitiesByHints(['hospital'], $lat, $lng, isEmergency: true);

        return [
            'type' => 'dispatch',
            'message' => $language === 'sw'
                ? 'Inaonekana unahitaji msaada kukujia. Mratibu atawasiliana nawe.'
                : 'It sounds like you need help to come to you. A coordinator will reach out.',
            'emergency_number' => null,
            'facilities' => $facilities,
        ];
    }
}
