<?php

namespace App\Providers;

use App\Support\ActivityStats;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();

            $view->with('layoutStreak', $user ? ActivityStats::forUser((string) $user->id, 35)['streak'] : 0);
        });
    }
}
