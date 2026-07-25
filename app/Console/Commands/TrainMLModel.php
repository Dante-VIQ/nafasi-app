<?php
// app/Console/Commands/TrainMlModel.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TrainMlModel extends Command
{
    protected $signature = 'ml:train';
    protected $description = 'Trigger daily ML model retraining';

    public function handle(): void
    {
        $this->info('Triggering ML training pipeline...');

        try {
            $response = Http::timeout(300) // 5 minutes
                ->post(config('services.ml_service.url') . '/train', [
                    'days' => 7,
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $this->info("Training complete. Version: {$result['version']}");
                $this->info("Interactions used: {$result['interactions_used']}");
                $this->info("New patterns: {$result['new_patterns_found']}");
            } else {
                $this->error('Training failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Training service unreachable: ' . $e->getMessage());
        }
    }
}