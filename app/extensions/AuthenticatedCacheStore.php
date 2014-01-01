<?php
use Illuminate\Cache\Repository;

class AuthenticatedCacheStore extends Illuminate\Cache\DatabaseStore
    implements Illuminate\Cache\StoreInterface
{


    public function get($key)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $key;
        }

        return parent::get($key);
    }

    public function put($key, $value, $minutes)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $key;
        }

        return parent::put($key, $value, $minutes);
    }

    public function increment($key, $value = 1)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $key;
        }

        return parent::increment($key, $value);
    }

    public function decrement($key, $value = 1)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $key;
        }

        return parent::increment($key, $value);
    }

    public function forever($key, $value)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $key;
        }

        return parent::forever($key, $value);
    }

    public function forget($key)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $key;
        }

        return parent::forget($key);
    }

    public function flush()
    {
        return parent::flush();
    }

    public function getPrefix()
    {
        return parent::getPrefix();

    }
}

Cache::extend(
    'authcache', function ($app) {
        $encrypter = $app['encrypter'];
        $table = $app['config']['cache.table'];

        $config = $app['config']['cache.connection'];

        $connection = $app['db']->connection($config);

        return new Repository(new AuthenticatedCacheStore($connection, $encrypter, $table));
    }
);