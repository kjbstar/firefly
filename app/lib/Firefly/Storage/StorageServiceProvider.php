<?php
namespace Firefly\Storage;

use Illuminate\Support\ServiceProvider;

class StorageServiceProvider extends ServiceProvider
{


    // Triggered automatically by Laravel
    public function register()
    {
        $this->app->bind(
            'Firefly\Helper\Account\AccountHelperInterface',
            'Firefly\Helper\Account\EloquentAccountHelper'
        );

        // storage:
        $this->app->bind(
            'Firefly\Storage\Account\AccountRepositoryInterface',
            'Firefly\Storage\Account\EloquentAccountRepository'
        );

        $this->app->bind(
            'Firefly\Storage\Setting\SettingRepositoryInterface',
            'Firefly\Storage\Setting\EloquentSettingRepository'
        );
        $this->app->bind(
            'Firefly\Storage\Transaction\TransactionRepositoryInterface',
            'Firefly\Storage\Transaction\EloquentTransactionRepository'
        );
    }

}