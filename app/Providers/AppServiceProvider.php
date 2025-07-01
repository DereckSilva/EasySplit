<?php

namespace App\Providers;

use App\Models\Expense;
use App\Observers\ExpenseObservable;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('App\Repository\ExpenseRepository');
        $this->app->bind('App\Repository\LogRepository');
        $this->app->bind('App\Repository\NotificationRepository');
        $this->app->bind('App\Repository\UserRepository');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Expense::observe(ExpenseObservable::class);
    }
}
