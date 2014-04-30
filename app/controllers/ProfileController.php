<?php

/**
 * Class ProfileController
 */
class ProfileController extends BaseController
{

    /**
     * Some general index. TODO
     */
    public function index()
    {
        return 'Hi there!';
    }

    /**
     * Change your password.
     *
     * @return \Illuminate\View\View
     */
    public function changePassword()
    {
        return View::make('profile.change-password');
    }

    public function postChangePassword()
    {
        if (!Hash::check(Input::get('current'), Auth::user()->getAuthPassword())) {
            Session::flash('error', 'Your current password is incorrect.');
            return View::make('profile.change-password');
        }

        if (Input::get('new') != Input::get('newagain')) {
            Session::flash('error', 'Try and type the same password twice.');
            return View::make('profile.change-password');
        }
        if (strlen(trim(Input::get('new'))) == 0 || strlen(trim(Input::get('newagain'))) == 0) {
            Session::flash('error', 'Fill in a new password as well.');
            return View::make('profile.change-password');
        }

        // finally!
        Auth::user()->password = Hash::make(Input::get('new'));
        Auth::user()->save();

        Session::flash('success', 'Your password has been changed.');
        return Redirect::route('home');
    }


}