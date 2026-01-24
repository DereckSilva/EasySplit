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
        // repositories
        $this->app->bind('App\Repository\Interfaces\ExpenseInterfaceRepository', 'App\Repository\ExpenseRepository');
        $this->app->bind('App\Repository\Interfaces\LogInterfaceRepository', 'App\Repository\LogRepository');
        $this->app->bind('App\Repository\NotificationRepository');
        $this->app->bind('App\Repository\Interfaces\UserInterfaceRepository', 'App\Repository\UserRepository');
        $this->app->bind('App\Repository\Interfaces\IntermediaryInterfaceRepository', 'App\Repository\IntermediaryRepository');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Expense::observe(ExpenseObservable::class);
    }
}
