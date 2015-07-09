<?php

namespace Scriptotek\Sru\Providers;

use Illuminate\Support\ServiceProvider;
use Scriptotek\Sru\Client as SruClient;

class SruServiceProviderLaravel4 extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('scriptotek/sru');
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;
        $app['sru-client'] = $app->share(function ($app) {
            return new SruClient($app['config']->get('sru-client::config'));
        });
        $app->alias('sru-client', 'Scriptotek\Sru\Client');
    }
}