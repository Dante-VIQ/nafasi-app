<?php
// app/Services/Verification/LlmVerifier.php

namespace App\Services\Verification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LlmVerifier
{
    protected string $endpoint;
    protected string $apiKey;
    protected string $mode; // 'real' or 'simulate'

    public function __construct()
    {
        $this->endpoint = config('services.llm.endpoint', '');
        $this->apiKey   = config('services.llm.api_key', '');
        $this->mode     = config('services.llm.mode', 'simulate');
    }

    /**
     * Verify the ML classification using an LLM.
     *
     * @param string $userText       Original user text (already anonymized)
     * @param array  $mlClassification The ML model's output
     * @return array
     */
    public function verify(string $userText, array $mlClassification): array
    {
        if ($this->mode === 'simulate') {
            return $this->simulate($userText, $mlClassification);
        }

        return $this->callRealLlm($userText, $mlClassification);
    }

    /**
     * Simulation mode — returns a pass-through with high confidence.
     */
    protected function simulate(string $userText, array $mlClassification): array
    {
        $language = $mlClassification['language'] ?? 'en';

        return [
            'verified'   => true,
            'confidence' => 0.92,
            'notes'      => $language === 'sw'
                ? 'LLM iko katika simulation mode. Uainishaji wa ML umepita.'
                : 'LLM is in simulation mode. ML classification passed through.',
            'mode'       => 'simulate',
        ];
    }

    /**
     * Call a real LLM API (e.g., OpenAI, Anthropic, or a local model on VPS).
     */
    protected function callRealLlm(string $userText, array $mlClassification): array
    {
        try {
            $prompt = $this->buildPrompt($userText, $mlClassification);

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($this->endpoint, [
                    'model'       => 'gpt-4o-mini', // or your local model
                    'messages'    => [
                        ['role' => 'system', 'content' => 'You are an emergency medical dispatcher for East Africa. Verify the AI classification. Respond with JSON only: {"verified": true/false, "confidence": 0.0-1.0, "notes": "brief explanation"}'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.1,
                    'max_tokens'  => 200,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '{}';
                $parsed = json_decode($content, true);

                return [
                    'verified'   => $parsed['verified'] ?? true,
                    'confidence' => $parsed['confidence'] ?? 0.5,
                    'notes'      => $parsed['notes'] ?? '',
                    'mode'       => 'real',
                ];
            }

            Log::warning('LLM API returned non-200', ['status' => $response->status()]);
            return $this->simulate($userText, $mlClassification);

        } catch (\Exception $e) {
            Log::error('LLM verification failed: ' . $e->getMessage());
            return $this->simulate($userText, $mlClassification);
        }
    }

    /**
     * Build a prompt for the LLM.
     */
    protected function buildPrompt(string $userText, array $mlClassification): string
    {
        $language  = $mlClassification['language'] ?? 'en';
        $intent    = json_encode($mlClassification['facility_hints'] ?? []);
        $emergency = $mlClassification['is_emergency'] ? 'YES' : 'NO';
        $crisis    = $mlClassification['is_crisis'] ? 'YES' : 'NO';

        return <<<EOT
User message (language: {$language}):
"{$userText}"

ML Classification:
- Intent: {$intent}
- Emergency: {$emergency}
- Crisis: {$crisis}
- Confidence: {$mlClassification['confidence']}

Verify if this classification is correct. Consider:
1. Does the intent match the user's request?
2. Is the emergency/crisis flag appropriate?
3. Is the confidence level reasonable?

Respond with JSON only.
EOT;
    }
}