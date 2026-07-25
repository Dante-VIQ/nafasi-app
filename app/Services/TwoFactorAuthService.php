<?php
// app/Services/TwoFactorAuthService.php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Enable 2FA for a user.
     */
    public function enable(User $user, string $method = 'app'): array
    {
        if ($method === 'app') {
            $secret = $this->google2fa->generateSecretKey();
            $qrCodeUrl = $this->google2fa->getQRCodeUrl(
                config('app.name', 'Nafasi'),
                $user->email,
                $secret
            );

            $user->update([
                'two_factor_secret' => $secret,
                'two_factor_method' => 'app',
                'two_factor_enabled' => false, 
            ]);

            return [
                'secret' => $secret,
                'qr_code_url' => $qrCodeUrl,
                'recovery_codes' => $this->generateRecoveryCodes($user),
            ];
        }

        // SMS method
        $user->update([
            'two_factor_method' => 'sms',
            'two_factor_enabled' => false,
        ]);

        return [
            'recovery_codes' => $this->generateRecoveryCodes($user),
        ];
    }

    /**
     * Confirm 2FA setup (after user verifies first code).
     */
    public function confirm(User $user): void
    {
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);
    }

    /**
     * Disable 2FA for a user.
     */
    public function disable(User $user): void
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_method' => 'app',
            'two_factor_code' => null,
            'two_factor_code_expires_at' => null,
            'two_factor_code_attempts' => 0,
        ]);
    }

    /**
     * Verify a 2FA code.
     */
    public function verify(User $user, string $code): bool
    {
        if ($user->locked_until && now()->lt($user->locked_until)) {
            return false;
        }

        if ($user->two_factor_method === 'app') {
            return $this->verifyAppCode($user, $code);
        }

        return $this->verifySmsCode($user, $code);
    }

    /**
     * Verify TOTP code from authenticator app.
     */
    protected function verifyAppCode(User $user, string $code): bool
    {
        try {
            $valid = $this->google2fa->verifyKey(
                $user->two_factor_secret,
                $code,
                0 // No window — strict verification
            );

            if (!$valid) {
                $this->incrementAttempts($user);
                return false;
            }

            $this->resetAttempts($user);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Verify SMS code.
     */
    protected function verifySmsCode(User $user, string $code): bool
    {
        if (!$user->two_factor_code_expires_at || now()->gt($user->two_factor_code_expires_at)) {
            return false;
        }

        if ($user->two_factor_code_attempts >= 5) {
            $user->update(['locked_until' => now()->addMinutes(15)]);
            return false;
        }

        if (!password_verify($code, $user->two_factor_code)) {
            $this->incrementAttempts($user);
            return false;
        }

        $user->update([
            'two_factor_code' => null,
            'two_factor_code_expires_at' => null,
            'two_factor_code_attempts' => 0,
        ]);

        return true;
    }

    /**
     * Verify a recovery code.
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $recoveryCodes = json_decode(
            decrypt($user->two_factor_recovery_codes),
            true
        );

        if (!is_array($recoveryCodes)) {
            return false;
        }

        $key = array_search($code, $recoveryCodes);

        if ($key === false) {
            $this->incrementAttempts($user);
            return false;
        }

        unset($recoveryCodes[$key]);

        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
        ]);

        $this->resetAttempts($user);
        return true;
    }

    /**
     * Generate new recovery codes.
     */
    public function generateRecoveryCodes(User $user): array
    {
        $codes = collect(range(1, 8))->map(function () {
            return Str::random(10) . '-' . Str::random(10);
        })->toArray();

        $user->update([
            'two_factor_recovery_codes' => encrypt(json_encode($codes)),
        ]);

        return $codes;
    }

    /**
     * Send an SMS code to the user.
     */
    public function sendSmsCode(User $user): bool
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'two_factor_code' => bcrypt($code),
            'two_factor_code_expires_at' => now()->addMinutes(5),
            'two_factor_code_attempts' => 0,
        ]);

        $phone = $user->phone_for_2fa ?? $user->phone;

        if (!$phone) {
            return false;
        }

        // In production: send SMS via Africa's Talking
        // For now, log the code
        \Illuminate\Support\Facades\Log::info("2FA code for {$phone}: {$code}");

        return true;
    }

    /**
     * Check if user has 2FA enabled and confirmed.
     */
    public function isEnabled(User $user): bool
    {
        return $user->two_factor_enabled && $user->two_factor_confirmed_at !== null;
    }

    /**
     * Increment failed 2FA attempts.
     */
    protected function incrementAttempts(User $user): void
    {
        $attempts = $user->two_factor_code_attempts + 1;
        $update = ['two_factor_code_attempts' => $attempts];

        if ($attempts >= 5) {
            $update['locked_until'] = now()->addMinutes(15);
        }

        $user->update($update);
    }

    /**
     * Reset 2FA attempts after successful verification.
     */
    protected function resetAttempts(User $user): void
    {
        $user->update([
            'two_factor_code_attempts' => 0,
            'locked_until' => null,
        ]);
    }
}