<?php
// app/Services/Risk/RiskAssessor.php

namespace App\Services\Risk;

use App\Models\InteractionOutcome;
use Illuminate\Support\Collection;

class RiskAssessor
{
    /**
     * The five urgency levels in descending order.
     */
    public const LEVELS = ['critical', 'high', 'medium', 'low', 'routine'];

    /**
     * Rules are evaluated in order. The first matching rule determines the level.
     * Each rule is a callable that receives the full assessment context and
     * returns true if the rule triggers.
     */
    protected array $rules = [];

    public function __construct()
    {
        $this->rules = $this->defaultRules();
    }

    /**
     * Assess the urgency of a situation.
     *
     * @param array $intent    From IntentClassifier::classify()
     * @param array $entities  From EntityExtractor::extract()
     * @param array $context   Optional extra context (tenant, time of day, etc.)
     * @return array{level: string, factors: array, confidence: float}
     */
    public function assess(array $intent, array $entities, array $context = []): array
    {
        $score = 0;
        $factors = [];

        // --- Critical signals (immediate life threat) ---------------------------------
        if ($intent['is_crisis'] ?? false) {
            return $this->result('critical', ['crisis_detected'], 1.0);
        }

        // Certain emergency types are always critical
        $criticalIntents = ['fire', 'snakebite', 'choking', 'cardiac', 'stroke'];
        if (!empty($intent['emergency_type']) && in_array($intent['emergency_type'], $criticalIntents)) {
            return $this->result('critical', ['critical_emergency_type'], 0.95);
        }

        // Specific symptoms that demand immediate attention
        $criticalSymptoms = ['unconscious', 'seizure', 'stroke', 'heart_attack', 'severe_bleeding', 'snake_bite', 'not_breathing'];
        $matchedSymptoms = array_intersect($entities['symptoms'] ?? [], $criticalSymptoms);
        if (!empty($matchedSymptoms)) {
            return $this->result('critical', ['critical_symptom'], 0.95);
        }

        // Severity + emergency
        if (($entities['severity'] ?? '') === 'severe' && ($intent['is_emergency'] ?? false)) {
            return $this->result('critical', ['severe_emergency'], 0.90);
        }

        // --- High urgency ------------------------------------------------------------------
        $highSymptoms = ['chest_pain', 'breathing', 'allergy', 'fracture', 'burn', 'pregnancy'];
        $matchedHigh = array_intersect($entities['symptoms'] ?? [], $highSymptoms);
        if (!empty($matchedHigh)) {
            $score += 3;
            $factors[] = 'high_symptom:' . implode(',', $matchedHigh);
        }

        if ($entities['time_urgency'] === 'immediate') {
            $score += 2;
            $factors[] = 'time_immediate';
        }

        // Vulnerable people
        $vulnerablePeople = ['child', 'elderly', 'pregnant'];
        $matchedVulnerable = array_intersect($entities['people'] ?? [], $vulnerablePeople);
        if (!empty($matchedVulnerable)) {
            $score += 2;
            $factors[] = 'vulnerable_person:' . implode(',', $matchedVulnerable);
        }

        // Strong dispatch request
        if ($intent['needs_dispatch'] ?? false) {
            $score += 2;
            $factors[] = 'dispatch_requested';
        }

        // --- Medium urgency ----------------------------------------------------------------
        $mediumSymptoms = ['headache', 'fever', 'vomiting', 'diarrhoea'];
        $matchedMedium = array_intersect($entities['symptoms'] ?? [], $mediumSymptoms);
        if (!empty($matchedMedium)) {
            $score += 1;
            $factors[] = 'medium_symptom';
        }

        if ($entities['severity'] === 'moderate') {
            $score += 1;
            $factors[] = 'moderate_severity';
        }

        if ($entities['time_urgency'] === 'recent') {
            $score += 1;
            $factors[] = 'time_recent';
        }

        // --- Low urgency -----------------------------------------------------------------
        if ($entities['severity'] === 'mild') {
            $score -= 1;
            $factors[] = 'mild_severity';
        }

        if ($entities['has_denial'] ?? false) {
            $score -= 1;
            $factors[] = 'denial_phrase';
        }

        if ($entities['has_medication_mention'] ?? false) {
            $score += 0; // neutral – could go either way
            $factors[] = 'medication_mentioned';
        }

        // --- Determine level from score --------------------------------------------------
        if ($score >= 3) {
            return $this->result('high', $factors, min(0.9, ($score / 5)));
        }
        if ($score >= 1) {
            return $this->result('medium', $factors, 0.7);
        }
        if ($score <= -1) {
            return $this->result('routine', $factors, 0.6);
        }
        return $this->result('low', $factors, 0.5);
    }

    /**
     * Build a result array.
     */
    protected function result(string $level, array $factors, float $confidence): array
    {
        return [
            'level'      => $level,
            'factors'    => $factors,
            'confidence' => $confidence,
        ];
    }

    /**
     * Default rules (can be extended).
     */
    protected function defaultRules(): array
    {
        return [];
    }
}