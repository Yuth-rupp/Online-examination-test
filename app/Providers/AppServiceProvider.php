<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use App\Observers\NotificationObserver;
use App\Models\AuditLog;
use App\Observers\AuditLogObserver;
use App\Support\Platform;

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

        // Share the platform name with EVERY view rendered anywhere in the
        // app — Super Admin, Admin, Teacher, Student, auth pages, emails —
        // without touching every controller. Change it once in Global
        // Settings and every role sees it on their very next page load.
        View::composer('*', function ($view) {
            $view->with('platformName', Platform::name());
            $view->with('platformNameSlug', Platform::slug());
        });
    }
}