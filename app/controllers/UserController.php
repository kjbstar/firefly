<?php

/**
 * Class UserController
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
        return View::make('user.login')->with('title', 'Login');
    }

    /**
     * Process the login
     *
     * @return View
     */
    public function postLogin()
    {
        if (Auth::attempt(
            ['email'    => Input::get('email'),
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
        return View::make('user.register')->with('title', 'Register');
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
        } else {
            sleep(4);
        }

        return View::make('user.sentpw')->with('title', 'Activated');

    }

    /**
     * Process the registration.
     *
     * @return \Illuminate\View\View
     */
    public function postRegister()
    {
        $data = ['email'      => Input::get('email'),
                 'activation' => Str::random(64),
                 'password'   => Str::random(60)];
        $user = new User($data);
        $validator = Validator::make($user->toArray(), User::$rules);
        if ($validator->fails()) {
            return View::make('user.register')->with(
                'warning', 'Invalid e-mail address.'
            )->with('title', 'Register');
        } else {
            $user->sendRegistrationMail();
            $user->save();

            return View::make('user.registered')->with('title', 'Registered!');
        }
    }

    /**
     * Allows the user to reset his password.
     *
     * @return \Illuminate\View\View
     */
    public function reset()
    {
        return View::make('user.reset')->with('title', 'Reset password');
    }


    /**
     * Process the reset request.
     *
     * @return \Illuminate\View\View
     */
    public function postReset()
    {
        $user = User::where('email', Input::get('email'))->whereNull('reset')
            ->first();
        if ($user) {
            $user->reset = Str::random(64);
            $user->save();
            $user->sendResetMail();
        } else {
            sleep(4);
        }

        return View::make('user.sent-reset')->with('title', 'Sent!');

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
        } else {
            sleep(4);
        }

        return View::make('user.sentpw')->with('title', 'Reset!');


    }

}
