<?php
// app/Services/NLU/EntityExtractor.php

namespace App\Services\NLU;

use App\Models\DictionaryEntry;
use Illuminate\Support\Str;

class EntityExtractor
{
    // Symptom patterns (English and Swahili)
    protected array $symptomPatterns = [
        'chest_pain'      => ['chest pain', 'maumivu ya kifua', 'kifua kuuma', 'heart pain'],
        'breathing'       => ['difficulty breathing', 'short of breath', 'can\'t breathe', 'kushindwa kupumua', 'kupumua kwa shida'],
        'bleeding'        => ['bleeding', 'damu', 'kutokwa damu', 'hemorrhage'],
        'seizure'         => ['seizure', 'convulsion', 'kifafa', 'mshtuko'],
        'headache'        => ['headache', 'kichwa kuuma', 'maumivu ya kichwa'],
        'fever'           => ['fever', 'homa', 'joto mwilini', 'temperature'],
        'vomiting'        => ['vomiting', 'kutapika', 'throwing up'],
        'diarrhoea'       => ['diarrhoea', 'kuhara', 'loose stool'],
        'fracture'        => ['fracture', 'broken bone', 'kuvunjika', 'mfupa'],
        'burn'            => ['burn', 'burnt', 'kuungua', 'kuchomeka'],
        'allergy'         => ['allergy', 'allergic', 'mzio', 'allergy reaction'],
        'snake_bite'      => ['snake bite', 'nyoka', 'snake', 'kung\'atwa na nyoka'],
        'unconscious'     => ['unconscious', 'fainted', 'passed out', 'kuzimia', 'amezimia'],
        'stroke'          => ['stroke', 'kiharusi', 'face drooping', 'arm weakness'],
        'heart_attack'    => ['heart attack', 'mshtuko wa moyo'],
        'pregnancy'       => ['pregnant', 'mimba', 'mjamzito', 'labour', 'kujifungua'],
    ];

    // Person indicators
    protected array $personPatterns = [
        'child'     => ['child', 'mtoto', 'baby', 'infant', 'mtoto mchanga'],
        'mother'    => ['mother', 'mama', 'mzazi', 'mom'],
        'elderly'   => ['elderly', 'old', 'mzee', 'wazee', 'grandfather', 'grandmother', 'babu', 'nyanya'],
        'pregnant'  => ['pregnant', 'mjamzito', 'expecting'],
        'self'      => ['me', 'myself', 'mimi', 'mwenyewe', 'i am', 'niko', 'nina'],
    ];

    // Time urgency indicators
    protected array $timePatterns = [
        'immediate' => ['right now', 'immediately', 'sasa hivi', 'mara moja', 'haraka', 'emergency', 'dharura'],
        'recent'    => ['since morning', 'today', 'leo', 'asubuhi', 'since yesterday', 'tangu jana'],
        'ongoing'   => ['for days', 'for hours', 'kwa siku', 'kwa masaa', 'all night', 'usiku kucha'],
    ];

    // Severity modifiers
    protected array $severityPatterns = [
        'severe'    => ['severe', 'very bad', 'extreme', 'kali', 'mbaya sana', 'sana', 'kubwa'],
        'moderate'  => ['moderate', 'wastani', 'somewhat', 'kiasi'],
        'mild'      => ['mild', 'slight', 'kidogo', 'ndogo'],
    ];

    /**
     * Extract all entities from user text.
     * Returns structured, validated array — never leaks raw text.
     */
    public function extract(string $text, string $language = 'en'): array
    {
        $textLower = Str::lower(trim($text));

        return [
            'symptoms'      => $this->extractSymptoms($textLower),
            'people'        => $this->extractPeople($textLower),
            'time_urgency'  => $this->extractTimeUrgency($textLower),
            'severity'      => $this->extractSeverity($textLower),
            'has_medication_mention' => $this->mentionsMedication($textLower),
            'has_denial'    => $this->hasDenial($textLower), // "I don't have..." / "sina..."
        ];
    }

    /**
     * Extract symptoms using hardcoded patterns + dictionary lookup.
     */
    protected function extractSymptoms(string $textLower): array
    {
        $found = [];

        foreach ($this->symptomPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($textLower, $pattern)) {
                    $found[] = $type;
                    break;
                }
            }
        }

        // Dictionary enhancement: search for emergency/medical tagged words
        $words = explode(' ', $textLower);
        foreach ($words as $word) {
            $entry = $this->lookupDictionary($word);
            if ($entry && in_array('medical', $entry['tags'] ?? [])) {
                $found[] = 'dictionary:' . $word;
            }
        }

        return array_unique($found);
    }

    /**
     * Extract person references.
     */
    protected function extractPeople(string $textLower): array
    {
        $found = [];
        foreach ($this->personPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($textLower, $pattern)) {
                    $found[] = $type;
                    break;
                }
            }
        }
        return array_unique($found);
    }

    /**
     * Determine time urgency.
     */
    protected function extractTimeUrgency(string $textLower): string
    {
        foreach ($this->timePatterns as $level => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($textLower, $pattern)) {
                    return $level;
                }
            }
        }
        return 'unknown';
    }

    /**
     * Determine severity level.
     */
    protected function extractSeverity(string $textLower): string
    {
        foreach ($this->severityPatterns as $level => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($textLower, $pattern)) {
                    return $level;
                }
            }
        }
        return 'unknown';
    }

    /**
     * Check if the text mentions medication.
     */
    protected function mentionsMedication(string $textLower): bool
    {
        $keywords = ['medicine', 'medication', 'dawa', 'tablet', 'pill', 'capsule', 'syrup', 'injection'];
        foreach ($keywords as $kw) {
            if (str_contains($textLower, $kw)) return true;
        }
        return false;
    }

    /**
     * Check if the text contains a denial phrase (important for risk assessment).
     */
    protected function hasDenial(string $textLower): bool
    {
        $denials = ['i don\'t have', 'sina', 'no ', 'not ', 'si ', 'hakuna', 'sio'];
        foreach ($denials as $d) {
            if (str_contains($textLower, $d)) return true;
        }
        return false;
    }

    /**
     * Look up a word in the dictionary for additional context.
     */
    protected function lookupDictionary(string $word): ?array
    {
        static $cache = [];
        if (array_key_exists($word, $cache)) return $cache[$word];

        $entry = DictionaryEntry::where('word_normalized', $word)
            ->orWhere('word', $word)
            ->first();

        return $cache[$word] = $entry ? [
            'tags' => $entry->tags ?? [],
            'emergency_weight' => $entry->emergency_weight ?? 0,
        ] : null;
    }
}