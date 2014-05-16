<?php
namespace Firefly\Storage;

use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider
{


    // Triggered automatically by Laravel
    public function register()
    {
        $this->app->bind(
            'Firefly\Storage\Account\AccountRepositoryInterface',
            'Firefly\Storage\Account\EloquentAccountRepository'
        );
    }

}