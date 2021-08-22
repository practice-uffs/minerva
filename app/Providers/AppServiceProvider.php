<?php

namespace App\Providers;

use App\Services\AuraNLP;
use App\Services\Telegram;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Telegram::class, function($app) {
            return new Telegram(config('telegram'));
        });

        $this->app->singleton(AuraNLP::class, function($app) {
            return new AuraNLP(config('auranlp'));
        });
    }
}
