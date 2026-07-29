<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InteractionOutcome extends Model
{
    protected $connection = 'mysql';   // ← force central database

    protected $fillable = [
        'uuid', 'tenant_id', 'session_id', 'user_text', 'language',
        'intent', 'confidence', 'facility_hints', 'recommended_facility_id',
        'outcome_type', 'outcome_facility_id', 'was_correct',
        'verified_by', 'verified_at', 'verification_notes', 'anonymized_text',
    ];

    protected $casts = [
        'intent'         => 'array',
        'facility_hints' => 'array',
        'confidence'     => 'float',
        'was_correct'    => 'boolean',
        'verified_at'    => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($outcome) {
            $outcome->uuid = (string) Str::uuid();
            $outcome->anonymized_text = self::anonymizeText($outcome->user_text);
        });
    }

    public static function anonymizeText(string $text): string
    {
        $text = preg_replace('/\+?\d{9,15}/', '[PHONE]', $text);
        $text = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[EMAIL]', $text);
        $text = preg_replace('/my name is \w+/i', 'my name is [PERSON]', $text);
        $text = preg_replace('/jina langu ni \w+/i', 'jina langu ni [PERSON]', $text);
        $text = preg_replace('/\b\d{7,8}\b/', '[ID]', $text);
        return $text;
    }
}