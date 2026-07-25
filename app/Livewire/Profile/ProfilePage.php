<?php
// app/Livewire/Profile/ProfilePage.php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilePage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $language_preference = 'sw';
    
    // Password change
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    
    public bool $saved = false;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->language_preference = $user->language_preference ?? 'sw';
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'language_preference' => 'required|in:sw,en,sheng',
        ];
    }

    public function updateProfile()
    {
        $this->validate();
        
        Auth::user()->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'language_preference' => $this->language_preference,
        ]);

        $this->saved = true;
        session()->flash('message', 'Profile updated successfully.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($this->current_password, Auth::user()->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('message', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.profile.profile-page')->layout('layouts.app');
    }
}