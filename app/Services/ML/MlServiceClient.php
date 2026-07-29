<?php

namespace App\Services\ML;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MlServiceClient
{
    protected string $mode; // 'php' or 'python'
    protected string $baseUrl;
    protected int $timeout;

    protected LanguageDetector $phpLanguageDetector;
    protected IntentClassifier $phpIntentClassifier;

    public function __construct()
    {
        $this->mode    = config('services.ml.mode', 'php');
        $this->baseUrl = config('services.ml.python_url', '');
        $this->timeout = config('services.ml.timeout', 3);

        // Always instantiate PHP classifiers (they're lightweight)
        $this->phpLanguageDetector = new LanguageDetector();
        $this->phpIntentClassifier = new IntentClassifier();
    }

    /**
     * Classify text – uses Python if available, otherwise PHP.
     */
    public function classify(string $text): array
    {
        if ($this->mode === 'python') {
            return $this->classifyViaPython($text);
        }
        return $this->classifyViaPhp($text);
    }

    /**
     * Detect language only.
     */
    public function detectLanguage(string $text): array
    {
        if ($this->mode === 'python') {
            return $this->detectLanguageViaPython($text);
        }
        return $this->phpLanguageDetector->detect($text);
    }

    /**
     * Health check for the Python service.
     */
    public function isHealthy(): bool
    {
        if ($this->mode === 'php') {
            return true; // PHP is always "healthy"
        }
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    // ----------------------------------------------------------------
    // PHP-based classification (uses the dictionary‑enriched classifiers)
    // ----------------------------------------------------------------
    protected function classifyViaPhp(string $text): array
    {
        $langResult = $this->phpLanguageDetector->detect($text);
        $language   = $langResult['language'];
        $intent     = $this->phpIntentClassifier->classify($text, $language);

        return array_merge($intent, [
            'text'                => $text,
            'language'            => $language,
            'language_confidence' => $langResult['confidence'],
            'source'              => 'php',
        ]);
    }

    // ----------------------------------------------------------------
    // Python-based classification (calls the Flask service on VPS)
    // ----------------------------------------------------------------
    protected function classifyViaPython(string $text): array
    {
        if (Cache::get('ml_service_failing', false)) {
            return $this->classifyViaPhp($text);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => config('services.ml.key', '')])
                ->post("{$this->baseUrl}/classify", ['text' => $text]);

            if ($response->successful()) {
                Cache::forget('ml_service_failing');
                return $response->json();
            }

            return $this->classifyViaPhp($text);

        } catch (\Exception $e) {
            Cache::put('ml_service_failing', true, 60);
            return $this->classifyViaPhp($text);
        }
    }

    protected function detectLanguageViaPython(string $text): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => config('services.ml.key', '')])
                ->post("{$this->baseUrl}/detect-language", ['text' => $text]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Fall back to PHP
        }
        return $this->phpLanguageDetector->detect($text);
    }
}