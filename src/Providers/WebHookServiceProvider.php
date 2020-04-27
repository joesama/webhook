<?php

namespace Joesama\Webhook\Providers;

use Joesama\Webhook\Web\Hook;
use Illuminate\Support\ServiceProvider;

class WebHookServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Hook::class, function ($app) {
            return new Hook();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Hook::class];
    }
}
