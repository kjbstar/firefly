<?php

class AuthenticatedCacheStore extends Illuminate\Cache\DatabaseStore
    implements Illuminate\Cache\StoreInterface
{

    public function get($key)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . '-' . $key;
        }

        return parent::get($key);
    }

    public function put($key, $value, $minutes)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . '-' . $key;
        }

        parent::put($key, $value, $minutes);
    }

    public function increment($key, $value = 1)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . '-' . $key;
        }

        parent::increment($key, $value);
    }

    public function decrement($key, $value = 1)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . '-' . $key;
        }

        parent::increment($key, $value);
    }

    public function forever($key, $value)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . '-' . $key;
        }

        parent::forever($key, $value);
    }

    public function forget($key)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . '-' . $key;
        }

        parent::forget($key);
    }

    public function flush()
    {
        parent::flush();
    }

    public function getPrefix()
    {
        parent::getPrefix();

    }

    public function has($key)
    {
        if (self::get($key)) {
            return true;
        }

        return false;
    }

}