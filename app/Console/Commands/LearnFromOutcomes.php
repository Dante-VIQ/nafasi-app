<?php
// app/Console/Commands/LearnFromOutcomes.php

namespace App\Console\Commands;

use App\Services\Learning\ContinuousLearner;
use Illuminate\Console\Command;

class LearnFromOutcomes extends Command
{
    protected $signature = 'nafasi:learn-from-outcomes';
    protected $description = 'Retrain the ML models using verified interaction outcomes';

    public function handle(): int
    {
        $this->info('Nafasi Continuous Learning Engine');
        $this->info('================================');

        $learner = new ContinuousLearner();
        $result = $learner->learn();

        $this->info("Status: {$result['status']}");
        $this->info("Correct outcomes: {$result['correct']}");
        $this->info("Incorrect outcomes: {$result['incorrect']}");
        $this->info("Dictionary updates: {$result['updates']}");

        return self::SUCCESS;
    }
}