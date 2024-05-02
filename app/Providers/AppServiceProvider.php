<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('CustomerService', \App\Services\CustomerService::class);
        $this->app->bind('LineBotService', \App\Services\LineBotService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ngrok使っているとroute()で生成されるURLがhttpsにならないときがあるのでその対策
        if ($this->app->environment('local')) {
            \URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();
        Paginator::defaultView('layouts.paginator');
    }
}
