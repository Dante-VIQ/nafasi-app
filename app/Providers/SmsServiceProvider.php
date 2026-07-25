<?php

namespace App\Providers;

use App\Channels\SmsChannel;
use App\Services\SmsService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SmsService::class, function () {
            return new SmsService();
        });
    }

    public function boot(): void
    {
        Notification::extend('sms', function ($app) {
            return $app->make(SmsChannel::class);
        });
    }
}