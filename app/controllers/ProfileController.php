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
        // some statistics:
        $stats = [];
        $stats['totalIn'] = floatval(Auth::user()->transactions()->incomes()->sum('amount'));
        $stats['totalOut'] = floatval(Auth::user()->transactions()->expenses()->sum('amount'));
        $stats['countIn'] = Auth::user()->transactions()->incomes()->count();
        $stats['countOut'] = Auth::user()->transactions()->expenses()->count();

        $stats['avgIn'] = $stats['countIn'] != 0 ? $stats['totalIn'] / $stats['countIn'] : $stats['totalIn'];
        $stats['avgOut'] = $stats['countOut'] != 0 ? $stats['totalOut'] / $stats['countOut'] : $stats['totalOut'];

        $stats['transferred'] = floatval(Auth::user()->transfers()->sum('amount'));
        $stats['transfers'] = Auth::user()->transfers()->count();
        $stats['types'] = [];
        foreach (Type::allTypes() as $type) {
            $stats['types'][Str::plural($type->type)] = Auth::user()->components()->whereTypeId($type->id)->count();
        }

        return View::make('profile.index')->with('title', 'Profile')->with('stats', $stats);
    }

    /**
     * Change your password.
     *
     * @return \Illuminate\View\View
     */
    public function changePassword()
    {
        return View::make('profile.change-password')->with('title', 'Change password');
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
        return Redirect::route('profile');
    }

    public function changeUsername()
    {
        return View::make('profile.change-username')->with('title', 'Change username');
    }

    public function postChangeUsername()
    {
        $count = User::whereUsername(Input::get('username'))->where('id', '!=', Auth::user()->id)->count();
        if($count > 0) {
            Session::flash('error', 'Could not change to this username.');
            return View::make('profile.change-username');
        }
        // is not changed?
        if(Auth::user()->username == Input::get('username')) {
            Session::flash('error', 'Have to change it buddy.');
            return View::make('profile.change-username');
        }

        // set!
        $user = Auth::user();
        $user->username = Input::get('username');

        $rules = [
            'username' => User::$rules['username']
        ];

        // validate:
        $validator = Validator::make($user->toArray(),$rules);
        if($validator->fails()) {
            Session::flash('error', $validator->messages()->first());
            return View::make('profile.change-username');
        }

        // save!
        $user->save();
        Session::flash('success', 'Username changed!');
        return Redirect::route('profile');
    }


}