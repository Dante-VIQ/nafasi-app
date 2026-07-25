<?php
// app/Services/Crisis/ChatEncryptionService.php

namespace App\Services\Crisis;

use Illuminate\Support\Str;

class ChatEncryptionService
{
    /**
     * Encrypt a message with a session-specific key.
     * Key is stored ONLY in cache — destroyed with session.
     */
    public function encrypt(string $message, string $sessionToken): string
    {
        $key = $this->getSessionKey($sessionToken);
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = sodium_crypto_secretbox($message, $nonce, $key);
        
        return base64_encode($nonce . $ciphertext);
    }

    /**
     * Decrypt a message with the session key.
     */
    public function decrypt(string $encrypted, string $sessionToken): string
    {
        $key = $this->getSessionKey($sessionToken);
        $decoded = base64_decode($encrypted);
        
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
        
        $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
        
        if ($plaintext === false) {
            throw new \Exception('Decryption failed — session may have expired');
        }
        
        return $plaintext;
    }

    /**
     * Get or create the session encryption key.
     * Stored in cache, destroyed when session ends.
     */
    protected function getSessionKey(string $sessionToken): string
    {
        $cacheKey = "chat_key_{$sessionToken}";
        
        $key = cache()->get($cacheKey);
        
        if (!$key) {
            $key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
            cache()->put($cacheKey, $key, now()->addHours(2));
        }
        
        return $key;
    }

    /**
     * Destroy the session key — all messages become unreadable.
     */
    public function destroyKey(string $sessionToken): void
    {
        cache()->forget("chat_key_{$sessionToken}");
    }
}