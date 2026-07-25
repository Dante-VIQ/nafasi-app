<?php
// app/Services/ML/MlServiceClient.php

namespace App\Services\ML;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MlServiceClient
{
    protected string $baseUrl;
    protected int $timeout;
    protected bool $circuitOpen = false;

    public function __construct()
    {
        $this->baseUrl = config('services.ml_service.url', 'http://127.0.0.1:5000');
        $this->timeout = config('services.ml_service.timeout', 3);
    }

    /**
     * Classify text using the ML service.
     * Falls back to keyword matching if service is unavailable.
     */
    public function classify(string $text): array
    {
        // Check circuit breaker
        if (Cache::get('ml_service_failing', false)) {
            return $this->fallbackClassify($text);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/classify", ['text' => $text]);

            if ($response->successful()) {
                // Reset circuit breaker on success
                Cache::forget('ml_service_failing');
                return $response->json();
            }

            Log::warning('ML service returned non-200', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            
            return $this->fallbackClassify($text);

        } catch (\Exception $e) {
            Log::error('ML service unavailable', ['error' => $e->getMessage()]);
            
            // Open circuit breaker for 60 seconds
            Cache::put('ml_service_failing', true, 60);
            
            return $this->fallbackClassify($text);
        }
    }

    /**
     * Detect language only.
     */
    public function detectLanguage(string $text): array
    {
        if (Cache::get('ml_service_failing', false)) {
            return ['language' => 'en', 'confidence' => 0.5];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/detect-language", ['text' => $text]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['language' => 'en', 'confidence' => 0.5];

        } catch (\Exception $e) {
            return ['language' => 'en', 'confidence' => 0.5];
        }
    }

    /**
     * Health check.
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Fallback classification when ML service is down.
     * Uses basic keyword matching.
     */
    protected function fallbackClassify(string $text): array
    {
        $text = strtolower($text);
        
        $result = [
            'text' => $text,
            'language' => 'en',
            'language_confidence' => 0.5,
            'is_crisis' => false,
            'is_emergency' => false,
            'emergency_type' => null,
            'facility_hints' => ['hospital'],
            'needs_dispatch' => false,
            'is_anonymous_report' => false,
            'confidence' => 0.3,
            'matched_signals' => ['fallback'],
            'source' => 'fallback',
        ];

        // Emergency keywords
        if (preg_match('/fire|moto|burning|smoke|accident|crash|bleeding|police|attacked|ambulance/', $text)) {
            $result['is_emergency'] = true;
            $result['confidence'] = 0.6;
        }

        // Crisis keywords
        if (preg_match('/suicide|kill myself|kujiua|want to die|end my life/', $text)) {
            $result['is_crisis'] = true;
            $result['confidence'] = 0.8;
        }

        // Facility hints
        if (preg_match('/pharmacy|dawa|chemist/', $text)) {
            $result['facility_hints'] = ['pharmacy'];
        } elseif (preg_match('/lab|maabara|test/', $text)) {
            $result['facility_hints'] = ['laboratory'];
        } elseif (preg_match('/dental|meno|tooth/', $text)) {
            $result['facility_hints'] = ['dental'];
        } elseif (preg_match('/maternity|mimba|pregnant/', $text)) {
            $result['facility_hints'] = ['maternity'];
        }

        return $result;
    }
}