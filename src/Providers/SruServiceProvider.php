<?php

namespace Scriptotek\Sru\Providers;

use Illuminate\Support\ServiceProvider;
use Scriptotek\Sru\Client as SruClient;

class SruServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(array(
            __DIR__.'/../../config/config.php' => config_path('sru.php')
        ));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php',
            'sru'
        );
        $this->app->singleton(SruClient::class, function ($app) {
            return new SruClient($app['config']->get('sru.endpoint'), $app['config']->get('sru'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SruClient::class];
    }
}
