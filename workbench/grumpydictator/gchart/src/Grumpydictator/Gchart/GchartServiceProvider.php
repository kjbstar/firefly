<?php

namespace Grumpydictator\Gchart;

use Illuminate\Support\ServiceProvider;

class GchartServiceProvider extends ServiceProvider {

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
  public function register() {
    $this->app['gchart'] = $this->app->share(function($app)
        {
            return new GChart;
        });
  }

  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot() {
    $this->package('grumpydictator/gchart');
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return array('gchart');
  }

}