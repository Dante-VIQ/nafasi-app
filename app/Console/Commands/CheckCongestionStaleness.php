<?php
// app/Console/Commands/CheckCongestionStaleness.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant\Facility;
use App\Models\Tenant\FacilityCongestionLog;

class CheckCongestionStaleness extends Command
{
    protected $signature = 'congestion:check-staleness';
    protected $description = 'Check for facilities with stale congestion data and penalize their routing priority.';

    public function handle(): void
    {
        $this->info('Checking congestion staleness...');

        $staleThreshold = now()->subHours(3);

        $staleFacilities = Facility::where('subscription_status', 'active')
            ->where(function ($query) use ($staleThreshold) {
                $query->where('congestion_updated_at', '<', $staleThreshold)
                    ->orWhereNull('congestion_updated_at');
            })
            ->where('is_active', true)
            ->get();

        $count = 0;
        foreach ($staleFacilities as $facility) {
            $facility->update(['routing_priority' => -5]);
            
            FacilityCongestionLog::create([
                'facility_id' => $facility->id,
                'status' => 'stale',
                'source' => 'auto',
                'notes' => 'Congestion status automatically degraded due to staleness. Last update: ' 
                    . ($facility->congestion_updated_at?->diffForHumans() ?? 'never'),
            ]);

            $count++;
        }

        $this->info("{$count} facilities penalized for stale congestion data.");
    }
}