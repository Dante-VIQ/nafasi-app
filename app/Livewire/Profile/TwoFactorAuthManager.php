<?php
// app/Livewire/Profile/TwoFactorAuthManager.php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Services\TwoFactorAuthService;
use Illuminate\Support\Facades\Auth;

class TwoFactorAuthManager extends Component
{
    public bool $enabled = false;
    public string $method = 'app';
    public bool $showSetup = false;
    public string $confirmationCode = '';

    public function mount()
    {
        $this->enabled = Auth::user()->hasTwoFactorEnabled();
        $this->method = Auth::user()->two_factor_method ?? 'app';
    }

    public function enable()
    {
        $service = app(TwoFactorAuthService::class);
        $service->enable(Auth::user(), $this->method);
        $this->showSetup = true;
    }

    public function confirm()
    {
        $this->validate(['confirmationCode' => 'required|string|size:6']);
        $service = app(TwoFactorAuthService::class);
        
        if ($service->verify(Auth::user(), $this->confirmationCode)) {
            $service->confirm(Auth::user());
            $this->enabled = true;
            $this->showSetup = false;
            session()->flash('message', 'Two-factor authentication enabled.');
        } else {
            $this->addError('confirmationCode', 'Invalid code.');
        }
    }

    public function disable()
    {
        $service = app(TwoFactorAuthService::class);
        $service->disable(Auth::user());
        $this->enabled = false;
        $this->showSetup = false;
        session()->flash('message', 'Two-factor authentication disabled.');
    }

    public function render()
    {
        return view('livewire.profile.two-factor-auth-manager');
    }
}