<?php

namespace App\Services\ML;

use App\Models\DictionaryEntry;

class LanguageDetector
{
    protected array $swahiliMarkers = [
        'na', 'ni', 'ya', 'za', 'kwa', 'hapa', 'niko', 'uko',
        'sina', 'kuna', 'nataka', 'nahitaji', 'nisaidie',
        'msaada', 'daktari', 'hospitali', 'maabara', 'dawa',
        'mimba', 'meno', 'macho', 'damu', 'moto', 'ajali',
    ];

    protected array $shengMarkers = [
        'noma', 'mboka', 'bazenga', 'chali', 'fika', 'gava',
        'dere', 'hosi', 'labu', 'daktar', 'kudedi', 'kuchapa',
        'kubambwa', 'nishike', 'niokoe', 'nimekwama', 'nadedi',
    ];

    public function detect(string $text): array
    {
        $textLower = strtolower(trim($text));
        $words     = explode(' ', $textLower);

        if (empty($textLower)) {
            return ['language' => 'en', 'confidence' => 0.5];
        }

        $swCount    = 0;
        $shengCount = 0;

        // Hardcoded markers first
        foreach ($words as $word) {
            if (in_array($word, $this->swahiliMarkers)) $swCount++;
            if (in_array($word, $this->shengMarkers)) $shengCount++;
        }

        // Swahili prefix pattern
        $swPrefixPattern = '/^(ana|ame|ali|ata|ina|zina|kina|vina|mna|wa|ki|vi|ji|ma|n|m)\w+/';
        foreach ($words as $word) {
            if (preg_match($swPrefixPattern, $word)) $swCount++;
        }

        // Dictionary lookup – uses your dataset
        foreach ($words as $word) {
            $lang = $this->detectWithDictionary($word);
            if ($lang === 'sw') $swCount++;
            if ($lang === 'sheng') $shengCount++;
        }

        $totalSignals = $swCount + $shengCount;

        if ($totalSignals === 0) {
            return ['language' => 'en', 'confidence' => 0.5];
        }

        if ($shengCount > $swCount && $shengCount >= 1) {
            $confidence = min(0.9, ($shengCount / count($words)) * 3);
            return ['language' => 'sheng', 'confidence' => round($confidence, 2)];
        }

        if ($swCount >= 2) {
            $confidence = min(0.95, ($swCount / count($words)) * 2);
            return ['language' => 'sw', 'confidence' => round($confidence, 2)];
        }

        return ['language' => 'en', 'confidence' => 0.6];
    }

    protected function detectWithDictionary(string $word): ?string
    {
        static $cache = [];

        if (array_key_exists($word, $cache)) {
            return $cache[$word];
        }

        $entry = DictionaryEntry::where('word_normalized', $word)
            ->orWhere('word', $word)
            ->whereIn('language', ['sw', 'sheng'])
            ->first();

        return $cache[$word] = $entry?->language;
    }
}