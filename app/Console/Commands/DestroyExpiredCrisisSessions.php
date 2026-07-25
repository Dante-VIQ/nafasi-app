<?php
// app/Console/Commands/DestroyExpiredCrisisSessions.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\CrisisChatSession;
use App\Services\Crisis\ChatEncryptionService;

class DestroyExpiredCrisisSessions extends Command
{
    protected $signature = 'crisis:destroy-expired';
    protected $description = 'Destroy expired crisis chat sessions and their encryption keys';

    public function handle(): void
    {
        $sessions = CrisisChatSession::where('auto_destroy_at', '<', now())
            ->orWhere('status', 'ended')
            ->get();

        $encryption = app(ChatEncryptionService::class);
        $count = 0;

        foreach ($sessions as $session) {
            // Destroy encryption key first
            $encryption->destroyKey($session->session_token);
            
            // Delete all messages (cascade)
            $session->delete();
            $count++;
        }

        $this->info("Destroyed {$count} expired crisis chat sessions.");
    }
}