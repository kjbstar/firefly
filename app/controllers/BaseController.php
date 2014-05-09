<?php

use Illuminate\Database\QueryException as QueryException;
use \Illuminate\Routing\Controller;

/**
 * Class BaseController
 *
 */
class BaseController extends Controller
{

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    /**
     * Get types.
     */
    public function __construct()
    {
        if (Cache::has('types')) {
            View::share('types', Cache::get('types'));
        } else {
            try {
                $types = Type::orderBy('type')->get();
            } catch (QueryException $e) {
                echo '<p>Database error. Did you run <span style="font-family:monospace;">
                php artisan migrate:refresh --seed</span>?</p>';
                echo '<p><span style="color:red;">Error:</span> '.$e->getMessage().'</p>';
                exit();
            }
            Cache::forever('types', $types);
            View::share('types', $types);
        }
    }
}
