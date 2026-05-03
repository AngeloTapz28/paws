<?php

namespace App\Providers;

use App\Models\Pet;
use App\Observers\PetObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\SystemNotification;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Bootstrap 5 pagination
        Paginator::useBootstrapFive();

        // Register PetObserver
        Pet::observe(PetObserver::class);

        // Share unread notification count to all views for authenticated users
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $unreadCount = SystemNotification::where('user_id', auth()->id())
                    ->whereNull('read_at')
                    ->count();

                $view->with('unreadNotificationCount', $unreadCount);
            }
        });
    }
}