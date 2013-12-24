<?php

namespace Grumpydictator\Gchart;

use Illuminate\Support\ServiceProvider;

/**
 * Class GchartServiceProvider
 *
 * @package Grumpydictator\Gchart
 */
class GchartServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //$this->app['gchart'] = new GChart;

      $this->app['gchart'] = function()
        {
            return new GChart;
        };
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('grumpydictator/gchart');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['gchart'];
    }

}