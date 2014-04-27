<?php
use Illuminate\Filesystem\Filesystem as Filesystem;

class AuthenticatedCacheFileStore extends Illuminate\Cache\FileStore
    implements Illuminate\Cache\StoreInterface
{
    private $_separator = '-';

    public function __construct(Filesystem $files, $directory)
    {
        parent::__construct($files, $directory);
    }


    public function get($key)
    {

        if (Auth::check()) {
            $key = Auth::user()->id . $this->_separator . $key;
        }
//        Log::debug('[get: "'.$key.'"]');
        return parent::get($key);
    }

    public function put($key, $value, $minutes)
    {
        if (Auth::check()) {
            DB::table('cachekeys')->insert(
                [
                    'user_id' => Auth::user()->id,
                    'key'     => $key
                ]
            );
            $key = Auth::user()->id . $this->_separator . $key;
        }
//        Log::debug('[put: "'.$key.'"]');

        parent::put($key, $value, $minutes);
    }

    public function increment($key, $value = 1)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $this->_separator . $key;
        }

        parent::increment($key, $value);
    }

    public function decrement($key, $value = 1)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $this->_separator . $key;
        }

        parent::increment($key, $value);
    }

    public function forever($key, $value)
    {
        parent::forever($key, $value);
    }

    public function forget($key)
    {
        if (Auth::check()) {
            $key = Auth::user()->id . $this->_separator . $key;
        }

        parent::forget($key);
    }

    public function flush()
    {
        parent::flush();
    }

    public function userFlush() {

        $result = DB::table('cachekeys')->whereUserId(Auth::user()->id)->get();

        foreach($result as $entry) {
            Cache::forget($entry->key);
        }
        DB::table('cachekeys')->whereUserId(Auth::user()->id)->delete();
    }

    public function getPrefix()
    {
        return parent::getPrefix();

    }

}