<?php
// app/Services/Decision/DecisionEngine.php

namespace App\Services\Decision;

class DecisionEngine
{
    /**
     * Available actions the engine can recommend.
     */
    public const ACTIONS = [
        'dispatch_ambulance',
        'recommend_emergency_room',
        'dispatch_responder',
        'transfer_to_nurse',
        'contact_emergency_hotline',
        'schedule_appointment',
        'connect_to_specialist',
        'self_care_guidance',
        'route_to_pharmacy',
        'route_to_laboratory',
        'general_facility_search',
    ];

    /**
     * Produce a recommended action based on AI signals.
     *
     * @param array $intent   From IntentClassifier::classify()
     * @param array $entities From EntityExtractor::extract()
     * @param array $risk     From RiskAssessor::assess()
     * @param array $context  Additional context (e.g. time of day, location)
     * @return array
     */
    public function decide(array $intent, array $entities, array $risk, array $context = []): array
    {
        $riskLevel = $risk['level'] ?? 'routine';

        // ── Critical / life-threatening ──────────────────────────────
        if ($riskLevel === 'critical') {
            // Fire or snakebite → dispatch immediately
            if (in_array($intent['emergency_type'] ?? '', ['fire', 'snakebite'])) {
                return $this->result('dispatch_responder', 0.98, ['critical_risk', 'specific_emergency_type']);
            }

            // Medical emergency with critical symptoms → ambulance
            $criticalSymptoms = ['unconscious', 'seizure', 'stroke', 'heart_attack', 'severe_bleeding', 'snake_bite'];
            if (!empty(array_intersect($entities['symptoms'] ?? [], $criticalSymptoms))) {
                return $this->result('dispatch_ambulance', 0.95, ['critical_risk', 'critical_symptom']);
            }

            // General critical → emergency room
            return $this->result('recommend_emergency_room', 0.92, ['critical_risk']);
        }

        // ── High urgency ─────────────────────────────────────────────
        if ($riskLevel === 'high') {
            if ($intent['needs_dispatch'] ?? false) {
                return $this->result('dispatch_responder', 0.88, ['high_risk', 'dispatch_requested']);
            }
            if ($intent['is_emergency'] ?? false) {
                return $this->result('recommend_emergency_room', 0.85, ['high_risk', 'emergency_intent']);
            }
            return $this->result('recommend_emergency_room', 0.80, ['high_risk']);
        }

        // ── Medium urgency ───────────────────────────────────────────
        if ($riskLevel === 'medium') {
            // Check facility hints for specific routing
            $hints = $intent['facility_hints'] ?? [];
            if (in_array('pharmacy', $hints)) {
                return $this->result('route_to_pharmacy', 0.78, ['medium_risk', 'pharmacy_hint']);
            }
            if (in_array('laboratory', $hints)) {
                return $this->result('route_to_laboratory', 0.78, ['medium_risk', 'lab_hint']);
            }
            if (in_array('hospital', $hints) || in_array('clinic', $hints)) {
                return $this->result('schedule_appointment', 0.75, ['medium_risk', 'facility_hint']);
            }
            return $this->result('schedule_appointment', 0.72, ['medium_risk']);
        }

        // ── Low / Routine ────────────────────────────────────────────
        $hints = $intent['facility_hints'] ?? [];
        if (in_array('pharmacy', $hints)) {
            return $this->result('route_to_pharmacy', 0.85, ['low_risk', 'pharmacy_hint']);
        }
        if (in_array('laboratory', $hints)) {
            return $this->result('route_to_laboratory', 0.85, ['low_risk', 'lab_hint']);
        }
        if ($entities['has_medication_mention'] ?? false) {
            return $this->result('route_to_pharmacy', 0.80, ['low_risk', 'medication_mention']);
        }
        if (in_array('hospital', $hints) || in_array('clinic', $hints)) {
            return $this->result('schedule_appointment', 0.75, ['low_risk', 'facility_hint']);
        }

        // Default
        return $this->result('general_facility_search', 0.70, ['no_specific_signals']);
    }

    /**
     * Format the decision result.
     */
    protected function result(string $action, float $confidence, array $factors): array
    {
        return [
            'action'     => $action,
            'confidence' => $confidence,
            'factors'    => $factors,
        ];
    }
}