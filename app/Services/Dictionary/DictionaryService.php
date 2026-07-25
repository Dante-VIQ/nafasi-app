<?php
// app/Services/Dictionary/DictionaryService.php

namespace App\Services\Dictionary;

use App\Models\DictionaryEntry;
use Illuminate\Support\Collection;

class DictionaryService
{
    /**
     * Look up a word in the dictionary.
     * Returns ALL meanings, not just one.
     */
    public function lookup(string $word, string $language = null): Collection
    {
        $normalized = $this->normalize($word);
        
        $query = DictionaryEntry::where('word_normalized', $normalized);
        
        if ($language) {
            $query->where('language', $language);
        }
        
        return $query->get();
    }

    /**
     * Find words that match ANY of the given tags.
     */
    public function findByTags(array $tags, string $language = null): Collection
    {
        $query = DictionaryEntry::query();
        
        foreach ($tags as $tag) {
            $query->whereJsonContains('tags', $tag);
        }
        
        if ($language) {
            $query->where('language', $language);
        }
        
        return $query->get();
    }

    /**
     * Get all emergency-signaling words for a language.
     */
    public function getEmergencyWords(string $language = 'sw'): Collection
    {
        return DictionaryEntry::where('language', $language)
            ->where('is_emergency_signal', true)
            ->orderBy('emergency_weight', 'desc')
            ->get();
    }

    /**
     * Get all crisis-signaling words.
     */
    public function getCrisisWords(string $language = 'sw'): Collection
    {
        return DictionaryEntry::where('language', $language)
            ->where('is_crisis_signal', true)
            ->get();
    }

    /**
     * Get words that suggest help should come to the patient.
     */
    public function getHelpComeToMeWords(string $language = 'sw'): Collection
    {
        return DictionaryEntry::where('language', $language)
            ->where('is_help_come_to_me_signal', true)
            ->get();
    }

    /**
     * Get facility-type-hinting words.
     */
    public function getFacilityHintWords(string $language = 'sw'): Collection
    {
        return DictionaryEntry::where('language', $language)
            ->whereNotNull('facility_type_hint')
            ->get();
    }

    /**
     * Search for words by definition (English).
     * Used when a facility defines emergency keywords in English
     * and we need Swahili/Sheng equivalents.
     */
    public function searchByDefinition(string $englishTerm): Collection
    {
        return DictionaryEntry::where('definition', 'like', "%{$englishTerm}%")
            ->orWhereJsonContains('meanings', $englishTerm)
            ->get();
    }

    /**
     * Get facility-linked keywords for a specific facility.
     */
    public function getFacilityKeywords(int $facilityId): Collection
    {
        return DictionaryEntry::whereHas('facilities', function ($query) use ($facilityId) {
            $query->where('facility_id', $facilityId)
                  ->where('relationship', 'keyword');
        })->get();
    }

    /**
     * Get facility exclusion keywords.
     */
    public function getFacilityExclusions(int $facilityId): Collection
    {
        return DictionaryEntry::whereHas('facilities', function ($query) use ($facilityId) {
            $query->where('facility_id', $facilityId)
                  ->where('relationship', 'exclusion');
        })->get();
    }

    /**
     * Normalize a word for lookup.
     */
    protected function normalize(string $word): string
    {
        return strtolower(trim($word));
    }

    /**
     * Detect the language of a text by querying the dictionary.
     * Returns the language with the most matching words.
     */
    public function detectLanguage(string $text): string
    {
        $words = explode(' ', strtolower($text));
        $scores = ['sw' => 0, 'en' => 0, 'sheng' => 0];

        foreach ($words as $word) {
            $entries = $this->lookup($word);
            foreach ($entries as $entry) {
                if (isset($scores[$entry->language])) {
                    $scores[$entry->language]++;
                }
            }
        }

        // Return language with highest score
        $max = max($scores);
        if ($max === 0) return 'en'; // Default
        
        return array_search($max, $scores);
    }
}