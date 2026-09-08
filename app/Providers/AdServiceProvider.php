<?php

namespace App\Providers;

use App\Services\AdService;
use Illuminate\Support\ServiceProvider;

class AdServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AdService::class, function ($app) {
            return new AdService();
        });
    }

    public function boot()
    {
        //
    }
}