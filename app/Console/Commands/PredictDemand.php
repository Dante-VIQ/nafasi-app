<?php
// app/Console/Commands/PredictDemand.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PredictDemand extends Command
{
    protected $signature = 'nafasi:predict-demand';
    protected $description = 'Predict demand for the coming period';

    public function handle(): void
    {
        $this->info('Predicting demand patterns...');

        try {
            $response = Http::timeout(30)
                ->get(config('services.ml_service.url') . '/predict/demand');

            if ($response->successful()) {
                $prediction = $response->json();
                
                $this->info("Predicted volume: {$prediction['predicted_volume']}");
                $this->info("Likely emergencies: " . implode(', ', $prediction['likely_emergency_types']));
                $this->info("Coordinators needed: {$prediction['recommended_staffing']['coordinators_needed']}");
                $this->info("Responders on standby: {$prediction['recommended_staffing']['responders_on_standby']}");
            }
        } catch (\Exception $e) {
            $this->error('Prediction engine unreachable: ' . $e->getMessage());
        }
    }
}