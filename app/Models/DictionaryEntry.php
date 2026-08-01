<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DictionaryEntry extends Model
{
     protected $connection = 'mysql';

    protected $fillable = [
        'word',
        'word_normalized',
        'language',
        'part_of_speech',
        'definition',
        'meanings',
        'source',
        'tags',
        'emergency_weight',
        'facility_type_hint',
        'is_crisis_signal',
        'is_help_come_to_me_signal',
        'is_emergency_signal',
        'synonyms',
        'antonyms',
        'usage_count',
        'confidence_score',
    ];

    protected $casts = [
        'meanings' => 'array',
        'tags' => 'array',
        'synonyms' => 'array',
        'antonyms' => 'array',
        'emergency_weight' => 'float',
        'confidence_score' => 'float',
        'is_crisis_signal' => 'boolean',
        'is_help_come_to_me_signal' => 'boolean',
        'is_emergency_signal' => 'boolean',
    ];

    /**
     * Facilities that have linked this word as a keyword.
     */
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Tenant\Facility::class,
            'facility_dictionary_links',
            'dictionary_entry_id',
            'facility_id'
        )->withPivot(['relevance_weight', 'relationship'])
         ->withTimestamps();
    }

    /**
     * Increment usage count when this word is matched.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Scope: words for a specific language.
     */
    public function scopeLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope: emergency signals only.
     */
    public function scopeEmergency($query)
    {
        return $query->where('is_emergency_signal', true);
    }

    /**
     * Scope: crisis signals only.
     */
    public function scopeCrisis($query)
    {
        return $query->where('is_crisis_signal', true);
    }
}