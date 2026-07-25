<?php
// app/Console/Commands/DestroyExpiredAssistanceRequests.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\AssistanceRequest;

class DestroyExpiredAssistanceRequests extends Command
{
    protected $signature = 'privacy:destroy-expired-requests';
    protected $description = 'Destroy assistance requests older than 24 hours';

    public function handle(): void
    {
        $count = AssistanceRequest::where('auto_destroy_at', '<', now())->delete();
        $this->info("Destroyed {$count} expired assistance requests.");
    }
}