<?php
// app/Services/Escalation/EscalationEngine.php

namespace App\Services\Escalation;

class EscalationEngine
{
    /**
     * Determine if a routing decision should be escalated to a human.
     *
     * @param array $risk     From RiskAssessor::assess()
     * @param array $decision From DecisionEngine::decide()
     * @param array $intent   From IntentClassifier::classify()
     * @return array
     */
    public function evaluate(array $risk, array $decision, array $intent): array
    {
        $riskLevel     = $risk['level'] ?? 'routine';
        $confidence    = $decision['confidence'] ?? 1.0;
        $isCrisis      = $intent['is_crisis'] ?? false;
        $isEmergency   = $intent['is_emergency'] ?? false;

        $shouldEscalate = false;
        $reasons = [];

        // 1. Crisis → always escalate
        if ($isCrisis) {
            $shouldEscalate = true;
            $reasons[] = 'crisis_detected';
        }

        // 2. Critical risk + confidence < 90% → escalate
        if ($riskLevel === 'critical' && $confidence < 0.90) {
            $shouldEscalate = true;
            $reasons[] = 'critical_low_confidence';
        }

        // 3. High risk + confidence < 80% → escalate
        if ($riskLevel === 'high' && $confidence < 0.80) {
            $shouldEscalate = true;
            $reasons[] = 'high_risk_low_confidence';
        }

        // 4. Emergency + confidence < 75% → escalate
        if ($isEmergency && $confidence < 0.75) {
            $shouldEscalate = true;
            $reasons[] = 'emergency_low_confidence';
        }

        // 5. Dispatch actions always get human review
        $dispatchActions = ['dispatch_ambulance', 'dispatch_responder'];
        if (in_array($decision['action'] ?? '', $dispatchActions)) {
            $shouldEscalate = true;
            $reasons[] = 'dispatch_action_requires_review';
        }

        return [
            'should_escalate' => $shouldEscalate,
            'reasons'         => $reasons,
            'escalation_level' => $shouldEscalate ? $this->determineEscalationLevel($risk, $decision) : 'none',
        ];
    }

    /**
     * Determine the urgency of the escalation.
     */
    protected function determineEscalationLevel(array $risk, array $decision): string
    {
        $riskLevel = $risk['level'] ?? 'routine';

        if (in_array($riskLevel, ['critical', 'high'])) {
            return 'immediate';
        }
        if ($riskLevel === 'medium') {
            return 'priority';
        }
        return 'standard';
    }
}