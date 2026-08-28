<?php

namespace App\Providers;

use App\Notifications\Channels\WebPushChannel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(app()->getLocale());

        Notification::extend('webpush', fn ($app) => $app->make(WebPushChannel::class));
    }
}
