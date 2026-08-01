<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CheckCongestionStaleness;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\TrainMlModel;
use App\Console\Commands\LearnFromInteractions;
use App\Console\Commands\PredictDemand;
use App\Console\Commands\DestroyExpiredCrisisSessions;
use App\Console\Commands\EnforceSubscriptions;
use App\Console\Commands\LearnFromOutcomes;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(LearnFromOutcomes::class)->dailyAt('03:00');
Schedule::command(CheckCongestionStaleness::class)->everyThirtyMinutes();

Schedule::command(TrainMlModel::class)->dailyAt('03:00'); // Train at 3 AM
Schedule::command(LearnFromInteractions::class, ['--days=7'])->dailyAt('03:00');
Schedule::command(PredictDemand::class)->dailyAt('06:00');
Schedule::command(DestroyExpiredCrisisSessions::class)->everyFifteenMinutes();
Schedule::command(EnforceSubscriptions::class)->daily();