<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Notification;
use App\Observers\NotificationObserver;
use App\Models\AuditLog;
use App\Observers\AuditLogObserver;

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
        // Every Notification::create(...) anywhere in the app now
        // broadcasts live to the owning user's private channel.
        Notification::observe(NotificationObserver::class);

        // Every AuditLog::create(...) anywhere in the app — including the
        // CaptureSuperAdminActivity catch-all middleware — now broadcasts
        // live to the Super Admin Forensic Audit Trails page in real time.
        AuditLog::observe(AuditLogObserver::class);
    }
}