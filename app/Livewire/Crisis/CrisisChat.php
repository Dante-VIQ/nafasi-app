<?php
// app/Livewire/Crisis/CrisisChat.php

namespace App\Livewire\Crisis;

use Livewire\Component;
use App\Models\Tenant\CrisisChatSession;
use App\Models\Tenant\CrisisChatMessage;
use App\Services\Crisis\ChatEncryptionService;
use Illuminate\Support\Str;

class CrisisChat extends Component
{
    public ?CrisisChatSession $session = null;
    public string $messageText = '';
    public array $messages = [];
    public string $sessionToken = '';
    public bool $isConnected = false;
    public bool $isWaiting = true;
    public string $crisisType = '';

    protected ChatEncryptionService $encryption;

    public function boot(ChatEncryptionService $encryption)
    {
        $this->encryption = $encryption;
    }

    public function mount(string $crisisType = 'general_crisis', string $language = 'sw')
    {
        $this->crisisType = $crisisType;
        
        // Create anonymous session
        $this->session = CrisisChatSession::create([
            'crisis_type' => $crisisType,
            'language' => $language,
            'status' => 'waiting',
            'general_area' => 'Not specified',
        ]);
        
        $this->sessionToken = $this->session->session_token;
        
        // Check if counselors are available
        $counselorsAvailable = \App\Models\User::role('coordinator')
            ->where('is_active', true)
            ->where('is_online', true)
            ->exists();
        
        if ($counselorsAvailable) {
            $this->connectToCounselor();
        }
    }

    public function connectToCounselor()
    {
        // Find available counselor
        $counselor = \App\Models\User::role('coordinator')
            ->where('is_active', true)
            ->where('is_online', true)
            ->first();
        
        if ($counselor && $this->session) {
            $this->session->update([
                'counselor_id' => $counselor->id,
                'status' => 'connected',
                'connected_at' => now(),
            ]);
            
            $this->isConnected = true;
            $this->isWaiting = false;
            
            // Send welcome message
            $this->addSystemMessage($this->getWelcomeMessage());
        }
    }

    public function sendMessage()
    {
        if (empty(trim($this->messageText)) || !$this->session) return;

        // Encrypt and store
        $encrypted = $this->encryption->encrypt($this->messageText, $this->sessionToken);
        
        CrisisChatMessage::create([
            'session_id' => $this->session->id,
            'content_encrypted' => $encrypted,
            'sender_type' => 'user',
            'sent_at' => now(),
        ]);

        // Add to local display
        $this->messages[] = [
            'content' => $this->messageText,
            'sender_type' => 'user',
            'sent_at' => now()->format('H:i'),
        ];

        $this->messageText = '';
        
        // In production: broadcast to counselor via WebSocket
    }

    public function loadMessages()
    {
        if (!$this->session) return;

        $this->messages = $this->session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(function ($msg) {
                return [
                    'content' => $this->encryption->decrypt($msg->content_encrypted, $this->sessionToken),
                    'sender_type' => $msg->sender_type,
                    'sent_at' => $msg->created_at->format('H:i'),
                ];
            })
            ->toArray();
    }

    public function endSession()
    {
        if ($this->session) {
            $this->session->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);
            
            // Destroy encryption key — messages become unreadable
            $this->encryption->destroyKey($this->sessionToken);
            
            $this->isConnected = false;
            $this->addSystemMessage('Chat ended. All messages have been destroyed.');
        }
    }

    protected function addSystemMessage(string $content)
    {
        CrisisChatMessage::create([
            'session_id' => $this->session->id,
            'content_encrypted' => $this->encryption->encrypt($content, $this->sessionToken),
            'sender_type' => 'system',
            'sent_at' => now(),
        ]);
        
        $this->messages[] = [
            'content' => $content,
            'sender_type' => 'system',
            'sent_at' => now()->format('H:i'),
        ];
    }

    protected function getWelcomeMessage(): string
    {
        if ($this->session->language === 'sw') {
            return "Habari. Umeunganishwa na mshauri. Unaweza kuzungumza kwa uhuru. Mazungumzo haya ni siri na yatafutwa ukiondoka.";
        }
        return "Hello. You're connected with a counselor. You can speak freely. This conversation is private and will be deleted when you leave.";
    }

    public function render()
    {
        return view('livewire.crisis.crisis-chat')->layout('layouts.guest');
    }
}