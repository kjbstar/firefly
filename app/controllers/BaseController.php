<?php

/**
 * Class BaseController
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

    public function __construct() {
        if(Cache::has('types')) {
            View::share('types', Cache::get('types'));
        } else {
            $types = Type::orderBy('type')->get();
            Cache::forever('types',$types);
            View::share('types', $types);
        }
    }
}
