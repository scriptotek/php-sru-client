<?php

namespace Scriptotek\Sru\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SruServiceProvider extends ServiceProvider
{

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
        $app = $this->app;
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php',
            'sru'
        );
        $app['sru-client'] = $app->share(function ($app) {
            return new SruClient($app['config']->get('sru.endpoint'), $app['config']->get('sru'));
        });
        $app->alias('sru-client', 'Scriptotek\Sru\Client');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('sru-client');
    }
}