<?php

namespace App\Providers;

use App\Models\{ Task, User };
use App\Observers\{ TaskObserver, UserObserver };
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Task::observe(TaskObserver::class);
    }
}
