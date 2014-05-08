<?php
use Illuminate\Filesystem\Filesystem as Filesystem;

/**
 * This class extends the very simple arraystore but adds the userFlush()
 * method which is used pretty often in Firefly.
 *
 * Class UserArrayStore
 */
class UserArrayStore extends Illuminate\Cache\ArrayStore
    implements Illuminate\Cache\StoreInterface
{
    public function userFlush() {
        return parent::flush();
    }
}

