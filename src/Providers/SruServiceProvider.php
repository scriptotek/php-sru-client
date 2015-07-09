<?php

namespace Scriptotek\Sru\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class SruServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Actual provider
     *
     * @var \Illuminate\Support\ServiceProvider
     */
    protected $provider;
    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);
        $this->provider = $this->getProvider();
    }
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        return $this->provider->boot();
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        return $this->provider->register();
    }
    /**
     * Return ServiceProvider according to Laravel version
     *
     * @return \Scriptotek\Sru\Provider\ProviderInterface
     */
    private function getProvider()
    {
        if (version_compare(Application::VERSION, '5.0', '<')) {
            $provider = '\Scriptotek\Sru\SruClientProviderLaravel4';
        } else {
            $provider = '\Scriptotek\Sru\SruClientProviderLaravel5';
        }
        return new $provider($this->app);
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