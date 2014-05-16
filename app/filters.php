<?php

use Carbon\Carbon as Carbon;


App::before(
    function ($request) {

        // currency
        View::share('currency', Config::get('firefly.currencies')[Setting::getSetting('currency')->value]['symbol']);

        // process session period
        $now = new Carbon;
        $now->modify('midnight');
        if (!Session::has('period')) {
            Session::put('period', $now);
        }

        if (Session::get('period') < $now) {
            // in the past:
            Session::put('when', -1);
            Session::get('period')->endOfMonth();
        } else {
            if ($now == Session::get('period')) {
                // now
                Session::put('when', 0);
            } else {
                // future
                Session::get('period')->startOfMonth();
                Session::put('when', 1);
            }
        }
    }
);

Route::filter(
    'meta', function ($response, $request) {
        $segment = $request->segment(2);
        if (!defined('OBJ')) {
            define('OBJ', Str::singular($segment));
        }
        if (!defined('OBJS')) {
            define('OBJS', Str::plural($segment));
        }


    }
);


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
