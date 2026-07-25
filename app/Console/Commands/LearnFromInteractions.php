<?php
// app/Console/Commands/LearnFromInteractions.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LearnFromInteractions extends Command
{
    protected $signature = 'nafasi:learn {--days=7}';
    protected $description = 'Trigger the intelligence engine to learn from recent interactions';

    public function handle(): void
    {
        $this->info('Nafasi Intelligence Engine — Learning from interactions...');

        try {
            $response = Http::timeout(300)
                ->post(config('services.ml_service.url') . '/learn', [
                    'days' => (int) $this->option('days'),
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $this->info("Status: {$result['status']}");
                $this->info("Interactions analyzed: {$result['interactions_analyzed']}");
                $this->info("New patterns found: {$result['new_patterns_found']}");
            } else {
                $this->error('Learning failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('Intelligence engine unreachable: ' . $e->getMessage());
        }
    }
}