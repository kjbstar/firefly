<?php
App::before(
    function ($request) {
        // add currency preference to view.
        /** @var $settings \Firefly\Storage\Setting\SettingRepositoryInterface */
        $settings = App::make('Firefly\Storage\Setting\SettingRepositoryInterface');
        $currency = $settings->getSettingValue('currency') ? : 0;
        View::share('currency', Config::get('firefly.currencies')[$currency]['symbol']);

        // types
        if (Cache::has('types')) {
            View::share('types', Cache::get('types'));
        } else {
            try {
                $types = Type::orderBy('type')->get();
            } catch (QueryException $e) {
                echo '<p>Database error. Did you follow the installation guidelines?</p>';
                echo '<p><span style="color:red;">Error:</span> ' . $e->getMessage() . '</p>';
                exit();
            }
            Cache::forever('types', $types);
            View::share('types', $types);
        }
    }
);

Route::filter('addAccountFilter', 'Firefly\Filter\AccountFilter@addAccount');
Route::filter('editAccountFilter', 'Firefly\Filter\AccountFilter@editAccount');

App::after(
    function ($request, $response) {
        //
    }
);

/*
  |--------------------------------------------------------------------------
  | Authentication Filters
  |--------------------------------------------------------------------------
  |
  | The following filters are used to verify that the user of the current
  | session is logged into this application. The "basic" filter easily
  | integrates HTTP Basic authentication for quick, simple checking.
  |
 */

Route::filter(
    'auth', function () {
        if (Auth::guest()) {
            return Redirect::guest('login');
        }
    }
);
// always authenticate home routes:
Route::when('home/*', 'auth');


Route::filter(
    'auth.basic', function () {
        return Auth::basic();
    }
);

/*
  |--------------------------------------------------------------------------
  | Guest Filter
  |--------------------------------------------------------------------------
  |
  | The "guest" filter is the counterpart of the authentication filters as
  | it simply checks that the current user is not logged in. A redirect
  | response will be issued if they are, which you may freely change.
  |
 */

Route::filter(
    'guest', function () {
        if (Auth::check()) {
            return Redirect::to('/');
        }
    }
);

/*
  |--------------------------------------------------------------------------
  | CSRF Protection Filter
  |--------------------------------------------------------------------------
  |
  | The CSRF filter is responsible for protecting your application against
  | cross-site request forgery attacks. If this special token in a user
  | session does not match the one given in this request, we'll bail.
  |
 */

Route::filter(
    'csrf', function () {
        if (Session::token() != Input::get('_token')) {
            throw new Illuminate\Session\TokenMismatchException;
        }
    }
);
