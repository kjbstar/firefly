<?php
/**
 * File contains the UserController.
 *
 * PHP version 5.5.6
 *
 * @category Controllers
 * @package  Ccontrollers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://geld.nder.dev/
 */

/**
 * Everything related to logging in and user stuff.
 *
 * Class UserController
 *
 * @category AccountController
 * @package  Controllers
 * @author   Sander Dorigo <sander@dorigo.nl>
 * @license  GPL 3.0
 * @link     http://www.sanderdorigo.nl/
 */
class UserController extends BaseController
{

    /**
     * Login view.
     *
     * @return View
     */
    public function login()
    {
        return View::make('user.login');
    }

    /**
     * Process the login
     *
     * @return View
     */
    public function doLogin()
    {
        if (Auth::attempt(
            ['email'   => Input::get('email'),
            'password' => Input::get('password')], true
        )
        ) {
            return Redirect::to('/home');
        } else {
            return View::make('user.login')->with(
                'warning', 'Incorrect login details'
            );
        }
    }

    /**
     * Logout the user.
     *
     * @return Redirect
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::to('/');
    }

    /**
     * Show the view to register as a new user.
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return View::make('user.register');
    }

    /**
     * Activates a new user
     *
     * @param string $code The code he is given.
     *
     * @return View
     */
    function activate($code)
    {
        if (Auth::check()) {
            App::abort(500);
        }
        $user = User::where('activation', $code)->first();
        if ($user) {
            $user->sendPasswordMail();
            $user->activation = null;
            $user->save();

            return View::make('user.sentpw');
        }

        App::abort(404);

        return View::make("error.404");
    }

    /**
     * Process the registration.
     *
     * TODO rename to "postRegister".
     *
     * @return \Illuminate\View\View
     */
    public function doRegister()
    {
        $user = new User(['email'     => Input::get('email'),
                         'activation' => Str::random(64),
                         'password'   => Str::random(60)]);
        $validator = Validator::make($user->toArray(), User::$rules);
        if ($validator->fails()) {
            return View::make('user.register')->with(
                'warning', 'Invalid e-mail address.'
            );
        } else {
            $user->sendRegistrationMail();
            $user->save();

            return View::make('user.registered');
        }
    }

    /**
     * Allows the user to reset his password.
     *
     * @return \Illuminate\View\View
     */
    public function reset()
    {
        return View::make('user.reset');
    }


    /**
     * Process the reset request.
     *
     * @return \Illuminate\View\View
     */
    public function doReset()
    {
        $user = User::where('email', Input::get('email'))->whereNull('reset')
            ->first();
        if ($user) {
            $user->reset = Str::random(64);
            $user->save();
            $user->sendResetMail();

            return View::make('user.sent-reset');
        }

        return View::make('user.reset')->with(
            'warning', 'Impossible or already reset.'
        );
    }

    /**
     * Give the user a new password.
     *
     * @param string $code The reset-code needed.
     *
     * @return \Illuminate\View\View
     */
    public function resetme($code)
    {
        $user = User::where('reset', $code)->first();
        if ($user) {
            $user->sendPasswordMail();
            $user->reset = null;
            $user->save();

            return View::make('user.sentpw');
        }

        App::abort(404);

        return View::make('error.404');
    }

}
