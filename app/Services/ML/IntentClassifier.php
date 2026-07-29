<?php

namespace App\Services\ML;

use App\Models\DictionaryEntry;

class IntentClassifier
{
    // Hardcoded keyword lists (unchanged)
    protected array $crisisSignals = [
        'sw'    => ['nataka kujiua', 'najiua', 'kujiua', 'nimetosheka', 'nataka kufa', 'nife'],
        'en'    => ['kill myself', 'end my life', 'suicide', 'want to die', 'better off dead'],
        'sheng' => ['nadedi', 'kujitoa', 'kujivuta', 'nimetosheka'],
    ];

    protected array $emergencySignals = [
        'fire' => [
            'sw'    => ['moto', 'ungua', 'moshi', 'inawaka'],
            'en'    => ['fire', 'burning', 'smoke', 'explosion', 'gas leak'],
            'sheng' => ['moto', 'kuwaka'],
        ],
        'accident' => [
            'sw'    => ['ajali', 'anguka', 'jeruhi', 'damu'],
            'en'    => ['accident', 'crash', 'collision', 'bleeding', 'injury'],
            'sheng' => ['ajali', 'kuchapa'],
        ],
        'medical' => [
            'sw'    => ['kupumua', 'zimia', 'maumivu', 'kifua'],
            'en'    => ['heart attack', 'stroke', 'unconscious', 'not breathing', 'choking'],
            'sheng' => ['kukataa', 'kudedi'],
        ],
        'police' => [
            'sw'    => ['wizi', 'shambulizi', 'polisi'],
            'en'    => ['police', 'attacked', 'robbed', 'gun', 'weapon', 'assault'],
            'sheng' => ['gava', 'kubambwa'],
        ],
    ];

    protected array $facilityHints = [
        'pharmacy'   => ['sw' => ['dawa','famasi','madawa'],       'en' => ['pharmacy','medicine','drug','prescription','chemist'], 'sheng' => ['famasi','dawa shop','madawa']],
        'laboratory' => ['sw' => ['maabara','kipimo','testi'],    'en' => ['lab','laboratory','test','blood test','scan'],        'sheng' => ['labu','maabara','testi']],
        'dental'     => ['sw' => ['meno','jino'],                 'en' => ['dental','tooth','teeth','dentist','gum'],              'sheng' => ['meno','meno doctor']],
        'maternity'  => ['sw' => ['mimba','mjamzito','kujifungua','uzazi'], 'en' => ['maternity','pregnant','pregnancy','delivery','antenatal'], 'sheng' => ['mzae','maternity','kuzaa']],
        'hospital'   => ['sw' => ['hospitali','spitali'],         'en' => ['hospital','emergency room','clinic'],                 'sheng' => ['hospitali','hosi','spitali']],
    ];

    protected array $helpComeToMe = [
        'sw'    => ['siwezi tembea','nimekwama','sina usafiri','kitandani','nimeanguka'],
        'en'    => ['can\'t move','stuck','alone','no transport','bedridden','fallen'],
        'sheng' => ['nimekwama','siwezi move','niko solo','niko chini'],
    ];

    protected array $reportSignals = [
        'sw'    => ['ripoti','taarifu','siri'],
        'en'    => ['report','anonymous','tip','witness'],
        'sheng' => ['ripoti','toa taarifa'],
    ];

    /**
     * Classify a user's text.
     */
    public function classify(string $text, string $language = 'en'): array
    {
        $textLower = strtolower(trim($text));
        $words     = explode(' ', $textLower);

        $result = [
            'is_crisis'           => false,
            'is_emergency'        => false,
            'emergency_type'      => null,
            'facility_hints'      => [],
            'needs_dispatch'      => false,
            'is_anonymous_report' => false,
            'confidence'          => 0.0,
            'matched_signals'     => [],
        ];

        // 1. Hardcoded crisis check (fast)
        $crisisWords = $this->crisisSignals[$language] ?? $this->crisisSignals['en'];
        foreach ($crisisWords as $crisisWord) {
            if (str_contains($textLower, $crisisWord)) {
                $result['is_crisis'] = true;
                $result['confidence'] = 0.95;
                $result['matched_signals'][] = "crisis:{$crisisWord}";
                return $result;
            }
        }

        // 2. Hardcoded emergency types
        foreach ($this->emergencySignals as $type => $signals) {
            $emergWords = $signals[$language] ?? $signals['en'] ?? [];
            foreach ($emergWords as $emergWord) {
                if (str_contains($textLower, $emergWord)) {
                    $result['is_emergency'] = true;
                    $result['emergency_type'] = $type;
                    $result['confidence'] = max($result['confidence'], 0.85);
                    $result['matched_signals'][] = "emergency:{$type}:{$emergWord}";
                }
            }
        }

        // 3. Hardcoded facility hints
        foreach ($this->facilityHints as $fType => $fSignals) {
            $fWords = $fSignals[$language] ?? $fSignals['en'] ?? [];
            foreach ($fWords as $fWord) {
                if (str_contains($textLower, $fWord) && !in_array($fType, $result['facility_hints'])) {
                    $result['facility_hints'][] = $fType;
                    $result['confidence'] = max($result['confidence'], 0.7);
                    $result['matched_signals'][] = "facility:{$fType}:{$fWord}";
                }
            }
        }

        // 4. Hardcoded dispatch signals
        $dispatchWords = $this->helpComeToMe[$language] ?? $this->helpComeToMe['en'];
        foreach ($dispatchWords as $dWord) {
            if (str_contains($textLower, $dWord)) {
                $result['needs_dispatch'] = true;
                $result['confidence'] = max($result['confidence'], 0.75);
                $result['matched_signals'][] = "dispatch:{$dWord}";
            }
        }

        // 5. Hardcoded report signals
        $reportWords = $this->reportSignals[$language] ?? $this->reportSignals['en'];
        foreach ($reportWords as $rWord) {
            if (str_contains($textLower, $rWord)) {
                $result['is_anonymous_report'] = true;
                $result['confidence'] = max($result['confidence'], 0.7);
                $result['matched_signals'][] = "report:{$rWord}";
            }
        }

        // 6. Dictionary‑based classification – OVERRIDES hardcoded results when it has higher confidence
        foreach ($words as $word) {
            $info = $this->getDictionaryInfo($word);
            if (!$info) continue;

            // Crisis takes absolute priority
            if ($info['is_crisis']) {
                $result['is_crisis'] = true;
                $result['confidence'] = 0.95;
                $result['matched_signals'][] = "dict:crisis:{$word}";
                return $result;
            }

            // Emergency signal from dictionary
            if ($info['is_emergency']) {
                $result['is_emergency'] = true;
                // Determine type from tags if possible
                foreach ($info['tags'] as $tag) {
                    if (array_key_exists($tag, $this->emergencySignals)) {
                        $result['emergency_type'] = $tag;
                        break;
                    }
                }
                $result['confidence'] = max($result['confidence'], $info['emergency_weight']);
                $result['matched_signals'][] = "dict:emergency:{$word}";
            }

            // Facility hint from dictionary
            if ($info['facility_hint'] && !in_array($info['facility_hint'], $result['facility_hints'])) {
                $result['facility_hints'][] = $info['facility_hint'];
                $result['confidence'] = max($result['confidence'], 0.7);
                $result['matched_signals'][] = "dict:facility:{$info['facility_hint']}:{$word}";
            }

            // Dispatch signal from dictionary
            if ($info['is_dispatch']) {
                $result['needs_dispatch'] = true;
                $result['confidence'] = max($result['confidence'], 0.75);
                $result['matched_signals'][] = "dict:dispatch:{$word}";
            }
        }

        // Default fallback if nothing matched
        if ($result['confidence'] === 0.0) {
            $result['confidence'] = 0.3;
            $result['facility_hints'] = ['hospital'];
        }

        return $result;
    }

    /**
     * Look up a word in the dictionary and return classification signals.
     * In‑memory cache per request to avoid hitting the DB for the same word.
     */
    protected function getDictionaryInfo(string $word): ?array
    {
        static $cache = [];

        if (array_key_exists($word, $cache)) {
            return $cache[$word];
        }

        $entry = DictionaryEntry::where('word_normalized', $word)
            ->orWhere('word', $word)
            ->first();

        return $cache[$word] = $entry ? [
            'tags'             => $entry->tags ?? [],
            'emergency_weight' => $entry->emergency_weight ?? 0,
            'facility_hint'    => $entry->facility_type_hint,
            'is_emergency'     => $entry->is_emergency_signal ?? false,
            'is_crisis'        => $entry->is_crisis_signal ?? false,
            'is_dispatch'      => $entry->is_help_come_to_me_signal ?? false,
        ] : null;
    }
}