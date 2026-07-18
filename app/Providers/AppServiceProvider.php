<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\Models\Notification;
use App\Observers\NotificationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the storage path to /tmp (the only writable directory on Vercel)
        if (env('APP_ENV') === 'production') {
            App::useStoragePath('/tmp');
        }

        // Every Notification::create(...) anywhere in the app now
        // broadcasts live to the owning user's private channel.
        Notification::observe(NotificationObserver::class);
    }
}