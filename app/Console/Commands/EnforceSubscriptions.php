<?php
// app/Console/Commands/EnforceSubscriptions.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\Facility;

class EnforceSubscriptions extends Command
{
    protected $signature = 'subscriptions:enforce';
    protected $description = 'Deactivate facilities with expired subscriptions';

    public function handle(): void
    {
        $expired = Facility::where('subscription_status', 'active')
            ->where('subscription_expires_at', '<', now())
            ->get();

        foreach ($expired as $facility) {
            $facility->update([
                'subscription_status' => 'past_due',
                'routing_priority'   => -50,  // Effectively invisible
            ]);
        }

        $this->info("Deactivated {$expired->count()} facilities.");
    }
}