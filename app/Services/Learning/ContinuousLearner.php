<?php
// app/Services/Learning/ContinuousLearner.php

namespace App\Services\Learning;

use App\Models\DictionaryEntry;
use App\Models\InteractionOutcome;
use Illuminate\Support\Facades\Log;

class ContinuousLearner
{
    /**
     * Learn from verified outcomes and update dictionary weights.
     * This runs as a scheduled job (daily at 3 AM).
     */
    public function learn(): array
    {
        // Only learn from verified outcomes
        $outcomes = InteractionOutcome::whereNotNull('was_correct')
            ->where('verified_at', '>=', now()->subDays(7))
            ->get();

        if ($outcomes->isEmpty()) {
            return ['status' => 'no_data', 'message' => 'No verified outcomes to learn from.'];
        }

        $updates = [];
        $correctCount = 0;
        $incorrectCount = 0;

        foreach ($outcomes as $outcome) {
            // Skip if no user text available
            if (empty($outcome->anonymized_text)) continue;

            $words = explode(' ', strtolower($outcome->anonymized_text));
            $adjustment = $outcome->was_correct ? 0.05 : -0.05; // Small increments

            foreach ($words as $word) {
                if (strlen($word) < 3) continue; // Skip very short words

                $entry = DictionaryEntry::where('word_normalized', $word)
                    ->orWhere('word', $word)
                    ->first();

                if ($entry) {
                    // Update emergency weight (clamped between 0 and 1)
                    $newWeight = max(0, min(1, $entry->emergency_weight + $adjustment));
                    $entry->update([
                        'emergency_weight' => $newWeight,
                        'usage_count'      => $entry->usage_count + 1,
                    ]);
                    $updates[] = "{$word}: {$entry->emergency_weight} → {$newWeight}";
                } else {
                    // New word – add to dictionary with baseline weight
                    if ($outcome->was_correct) {
                        DictionaryEntry::create([
                            'word'               => $word,
                            'word_normalized'    => $word,
                            'language'           => $outcome->language ?? 'unknown',
                            'emergency_weight'   => 0.3,
                            'usage_count'        => 1,
                            'source'             => 'learned',
                            'tags'               => $outcome->facility_hints ?? [],
                            'is_emergency_signal' => ($outcome->risk_assessment['level'] ?? '') === 'critical',
                        ]);
                        $updates[] = "{$word}: NEW (baseline 0.3)";
                    }
                }
            }

            if ($outcome->was_correct) $correctCount++;
            else $incorrectCount++;
        }

        Log::info('ContinuousLearner: Training complete', [
            'correct'   => $correctCount,
            'incorrect' => $incorrectCount,
            'updates'   => count($updates),
        ]);

        return [
            'status'    => 'completed',
            'correct'   => $correctCount,
            'incorrect' => $incorrectCount,
            'updates'   => count($updates),
            'message'   => "Learned from {$correctCount} correct and {$incorrectCount} incorrect outcomes.",
        ];
    }
}